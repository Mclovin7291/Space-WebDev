<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}
include('../includes/db.php');
include('../includes/header.php');

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate and sanitize inputs
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $discovery_date = $_POST['discovery_date'];
    $category = $_POST['category'];
    $image_url = filter_var($_POST['image_url'], FILTER_SANITIZE_URL);
    
    // Validate URL length
    if (strlen($image_url) > 2048) {
        $error = "Image URL is too long. Maximum length is 2048 characters.";
    } else {
        $stmt = $conn->prepare("UPDATE space_discoveries SET 
            title = :title,
            description = :description,
            discovery_date = :discovery_date,
            category = :category,
            image_url = :image_url
            WHERE id = :id AND user_id = :user_id");
        
        try {
            $stmt->execute([
                'title' => $title,
                'description' => $description,
                'discovery_date' => $discovery_date,
                'category' => $category,
                'image_url' => $image_url,
                'id' => $id,
                'user_id' => $_SESSION['user_id']
            ]);
            
            header('Location: /webdev/pages/view_records.php');
            exit;
        } catch (PDOException $e) {
            $error = "Error updating record. Please try again.";
        }
    }
}

$stmt = $conn->prepare("SELECT * FROM space_discoveries WHERE id = :id");
$stmt->execute(['id' => $id]);
$discovery = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$discovery) {
    header('Location: /webdev/pages/view_records.php');
    exit;
}
?>

<main>
    <div class="form-container">
        <h2>Edit Discovery</h2>
        <form method="post" id="editForm" class="discovery-form">
            <div class="form-group">
                <label for="title">Discovery Title</label>
                <input type="text" id="title" name="title" 
                       value="<?php echo htmlspecialchars($discovery['title']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" required><?php echo htmlspecialchars($discovery['description']); ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="discovery_date">Discovery Date</label>
                <input type="date" id="discovery_date" name="discovery_date" 
                       value="<?php echo $discovery['discovery_date']; ?>" required>
            </div>
            
            <div class="form-group">
                <label for="category">Category</label>
                <select id="category" name="category" required>
                    <?php
                    $categories = ['planet', 'star', 'galaxy', 'nebula'];
                    foreach ($categories as $category) {
                        $selected = ($category == $discovery['category']) ? 'selected' : '';
                        echo "<option value='$category' $selected>$category</option>";
                    }
                    ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="image_url">Image URL</label>
                <input type="url" id="image_url" name="image_url" 
                       value="<?php echo htmlspecialchars($discovery['image_url']); ?>"
                       placeholder="https://example.com/image.jpg">
                <small class="form-help">Enter a direct image URL (should end with .jpg, .png, .gif, etc)<br>
                Right-click on an image and select "Copy image address" to get the direct URL</small>
            </div>
            
            <div class="form-group">
                <button type="submit" class="submit-btn">Save Changes</button>
            </div>
        </form>
    </div>
</main>

<?php include('../includes/footer.php'); ?>
