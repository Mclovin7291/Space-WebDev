<?php
session_start();
include('../includes/header.php');
?>

<main>
    <div class="home-container">
        <h2>Explore the Wonders of Space</h2>
        <p>Join us on a journey beyond the stars!</p>
        
        <?php if (!isset($_SESSION['username'])): ?>
            <div class="cta-buttons">
                <a href="/webdev/pages/signup.php" class="cta-button">Get Started</a>
                <a href="/webdev/pages/login.php" class="cta-button secondary">Login</a>
            </div>
        <?php else: ?>
            <div class="cta-buttons">
                <a href="/webdev/pages/dashboard.php" class="cta-button">Dashboard</a>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php include('../includes/footer.php'); ?>
