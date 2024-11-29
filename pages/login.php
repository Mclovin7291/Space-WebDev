<?php
session_start();
include('../includes/db.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['username'] = $user['username'];
        $_SESSION['user_id'] = $user['id'];
        header('Location: /webdev/pages/dashboard.php');
        exit;
    } else {
        $error = "Invalid credentials!";
    }
}
?>

<?php include('../includes/header.php'); ?>

<main>
    <div class="form-container">
        <h2>Login</h2>
        <?php if (isset($error)): ?>
            <div class="error-message">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <form method="post" action="/webdev/pages/login.php">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <div class="form-group">
                <label>
                    <input type="checkbox" name="remember_me"> Remember me
                </label>
            </div>
            
            <button type="submit">Login</button>
        </form>
        
        <p class="form-footer">
            Don't have an account? <a href="/pages/signup.php">Sign up here</a>
        </p>
    </div>
</main>

<?php include('../includes/footer.php'); ?>
