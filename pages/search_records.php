<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: /webdev/pages/login.php');
    exit;
}
include('../includes/db.php');
include('../includes/header.php');

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$category = isset($_GET['category']) ? $_GET['category'] : '';

$sql = "SELECT * FROM space_discoveries WHERE user_id = :user_id";
$params = ['user_id' => $_SESSION['user_id']];

if (!empty($search)) {
    $sql .= " AND (title LIKE :search OR description LIKE :search)";
    $params['search'] = "%$search%";
}

if (!empty($category)) {
    $sql .= " AND category = :category";
    $params['category'] = $category;
}

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$discoveries = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<main>
    <div class="search-container">
        <h2>Search Discoveries</h2>
        <form method="GET" class="search-form">
            <div class="form-group">
                <input type="text" name="search" placeholder="Search by title or description" 
                       value="<?php echo htmlspecialchars($search); ?>">
            </div>
            <div class="form-group">
                <select name="category">
                    <option value="">All Categories</option>
                    <option value="Planet" <?php echo $category === 'Planet' ? 'selected' : ''; ?>>Planet</option>
                    <option value="Star" <?php echo $category === 'Star' ? 'selected' : ''; ?>>Star</option>
                    <option value="Galaxy" <?php echo $category === 'Galaxy' ? 'selected' : ''; ?>>Galaxy</option>
                    <option value="Nebula" <?php echo $category === 'Nebula' ? 'selected' : ''; ?>>Nebula</option>
                    <option value="Black Hole" <?php echo $category === 'Black Hole' ? 'selected' : ''; ?>>Black Hole</option>
                    <option value="Other" <?php echo $category === 'Other' ? 'selected' : ''; ?>>Other</option>
                </select>
            </div>
            <button type="submit">Search</button>
        </form>

        <div class="search-results">
            <?php if (empty($discoveries)): ?>
                <p class="no-results">No discoveries found matching your criteria.</p>
            <?php else: ?>
                <div class="discoveries-grid">
                    <?php foreach ($discoveries as $discovery): ?>
                        <!-- Use your existing discovery card layout here -->
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php include('../includes/footer.php'); ?> 