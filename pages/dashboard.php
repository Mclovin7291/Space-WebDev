<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: /webdev/pages/login.php');
    exit;
}
include('../includes/header.php');
?>

<main>
    <div class="dashboard-container">
        <h2>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
        <div class="dashboard-menu">
            <a href="create_record.php" class="dashboard-link">Add New Space Discovery</a>
            <a href="view_records.php" class="dashboard-link">View Discoveries</a>
            <a href="edit_records.php" class="dashboard-link">Edit Discoveries</a>
        </div>
    </div>
</main>

<?php include('../includes/footer.php'); ?>
