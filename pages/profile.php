<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: /webdev/pages/login.php');
    exit;
}
include('../includes/db.php');
include('../includes/header.php');

// Get user stats
$stmt = $conn->prepare("SELECT COUNT(*) as total, category, 
                       COUNT(CASE WHEN MONTH(discovery_date) = MONTH(CURRENT_DATE) 
                             AND YEAR(discovery_date) = YEAR(CURRENT_DATE) 
                             THEN 1 END) as this_month 
                       FROM space_discoveries 
                       WHERE user_id = :user_id 
                       GROUP BY category");
$stmt->execute(['user_id' => $_SESSION['user_id']]);
$stats = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<main>
    <div class="profile-container">
        <h2>My Profile</h2>
        
        <div class="profile-stats">
            <div class="stats-card">
                <h3>Discovery Statistics</h3>
                <div class="stats-grid">
                    <?php foreach ($stats as $stat): ?>
                        <div class="stat-item">
                            <h4><?php echo htmlspecialchars($stat['category']); ?></h4>
                            <p>Total: <?php echo $stat['total']; ?></p>
                            <p>This Month: <?php echo $stat['this_month']; ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="profile-actions">
            <a href="change_password.php" class="action-btn">Change Password</a>
            <a href="edit_profile.php" class="action-btn">Edit Profile</a>
        </div>
    </div>
</main>

<?php include('../includes/footer.php'); ?> 