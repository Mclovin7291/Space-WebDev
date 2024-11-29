<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
    
    // Check for remember me cookie
    if (!isset($_SESSION['username']) && isset($_COOKIE['remember_user'])) {
        // Verify cookie token and log user in
        include_once('../includes/db.php');
        $token = $_COOKIE['remember_user'];
        $stmt = $conn->prepare("SELECT users.* FROM users 
                              JOIN user_tokens ON users.id = user_tokens.user_id 
                              WHERE token = ? AND expires > NOW()");
        $stmt->execute([$token]);
        $user = $stmt->fetch();
        if ($user) {
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_id'] = $user['id'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Space Explorer</title>
    <link rel="stylesheet" href="/webdev/css/styles.css">
</head>
<body>
    <div class="stars"></div>
    <header>
        <nav>
            <div class="nav-left">
                <a href="/webdev/index.php">Home</a>
                <a href="/webdev/pages/about.php">About</a>
                <?php if (isset($_SESSION['username'])): ?>
                    <a href="/webdev/pages/dashboard.php">Dashboard</a>
                    <a href="/webdev/pages/view_records.php">View Records</a>
                    <a href="/webdev/pages/search_records.php">Search</a>
                    <a href="/webdev/pages/profile.php">Profile</a>
                <?php endif; ?>
                <a href="/webdev/pages/faq.php">FAQ</a>
            </div>
            
            <div class="nav-right">
                <?php if (isset($_SESSION['username'])): ?>
                    <span class="username-display">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                    <a href="/webdev/pages/logout.php" class="logout-btn">Logout</a>
                <?php else: ?>
                    <a href="/webdev/pages/login.php">Login</a>
                    <a href="/webdev/pages/signup.php">Sign Up</a>
                <?php endif; ?>
            </div>
        </nav>
    </header>
