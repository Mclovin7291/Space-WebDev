<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: /webdev/pages/login.php');
    exit;
}
include('../includes/db.php');
include('../includes/header.php');

// Get only the current user's discoveries
$stmt = $conn->prepare("SELECT * FROM space_discoveries WHERE user_id = :user_id ORDER BY created_at DESC");
$stmt->execute(['user_id' => $_SESSION['user_id']]);
$discoveries = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<main>
    <div class="records-container">
        <h2>My Space Discoveries</h2>
        <?php if (empty($discoveries)): ?>
            <p class="no-records">No discoveries yet. <a href="/webdev/pages/create_record.php">Add your first discovery!</a></p>
        <?php else: ?>
            <div class="discoveries-grid">
                <?php foreach ($discoveries as $discovery): ?>
                    <div class="discovery-card">
                        <div class="image-container">
                            <?php 
                            $imageUrl = $discovery['image_url'];
                            // Debug output
                            echo "<!-- Debug: Image URL = " . htmlspecialchars($imageUrl) . " -->";
                            ?>
                            <img 
                                src="<?php echo !empty($imageUrl) ? htmlspecialchars($imageUrl) : '/webdev/images/default-space.jpg'; ?>" 
                                alt="<?php echo htmlspecialchars($discovery['title']); ?>"
                                class="discovery-image"
                                onerror="this.onerror=null; console.log('Image failed to load:', this.src); this.src='/webdev/images/default-space.jpg';"
                            >
                        </div>
                        <div class="discovery-content">
                            <h3><?php echo htmlspecialchars($discovery['title']); ?></h3>
                            <p class="discovery-date">Discovered: <?php echo date('M d, Y', strtotime($discovery['discovery_date'])); ?></p>
                            <p class="discovery-category"><?php echo htmlspecialchars($discovery['category']); ?></p>
                            <p class="discovery-description"><?php echo htmlspecialchars($discovery['description']); ?></p>
                            <div class="discovery-actions">
                                <a href="/webdev/pages/edit_records.php?id=<?php echo $discovery['id']; ?>" class="edit-btn">Edit</a>
                                <button onclick="confirmDelete(<?php echo $discovery['id']; ?>)" class="delete-btn">Delete</button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.discovery-image').forEach(img => {
            img.addEventListener('load', function() {
                console.log('Image loaded successfully:', this.src);
            });
            
            img.addEventListener('error', function() {
                console.log('Image failed to load:', this.src);
                if (this.src !== '/webdev/images/default-space.jpg') {
                    this.src = '/webdev/images/default-space.jpg';
                }
            });
        });
    });

    function confirmDelete(id) {
        if (confirm('Are you sure you want to delete this discovery?')) {
            window.location.href = `/webdev/pages/delete_records.php?id=${id}`;
        }
    }
    </script>
</main>

<?php include('../includes/footer.php'); ?>
