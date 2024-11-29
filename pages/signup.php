<?php
session_start();
include('../includes/db.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Basic validation
    $errors = [];
    
    if (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters long";
    }
    
    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match";
    }

    // Check if username already exists
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->execute(['username' => $username]);
    if ($stmt->rowCount() > 0) {
        $errors[] = "Username already taken";
    }

    // If no errors, proceed with registration
    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        try {
            $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (:username, :email, :password)");
            $stmt->execute([
                'username' => $username,
                'email' => $email,
                'password' => $hashed_password
            ]);
            
            $_SESSION['success'] = "Registration successful! Please login.";
            header('Location: /webdev/pages/login.php');
            exit;
        } catch (PDOException $e) {
            $errors[] = "Registration failed. Please try again.";
        }
    }
}
?>

<?php include('../includes/header.php'); ?>

<main>
    <h2>Sign Up for Space Explorer</h2>
    
    <?php if (!empty($errors)): ?>
        <div class="error-messages">
            <?php foreach ($errors as $error): ?>
                <p class="error"><?php echo htmlspecialchars($error); ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form action="/webdev/pages/signup.php" method="post">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" required>

        <label for="email">Email</label>
        <input type="email" id="email" name="email" required>

        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>

        <label for="confirm_password">Confirm Password</label>
        <input type="password" id="confirm_password" name="confirm_password" required>

        <button type="submit">Sign Up</button>
    </form>
</main>

<?php include('../includes/footer.php'); ?>
