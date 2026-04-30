<?php
require_once 'includes/functions.php';

if (isLoggedIn()) {
    redirect('index.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user'] = $user;

        $redirect = $_SESSION['redirect_after_login'] ?? 'index.php';
        unset($_SESSION['redirect_after_login']);
        redirect($redirect);
    } else {
        setFlash('Invalid email or password', 'error');
    }
}

require_once 'includes/header.php';
?>

<main>
    <section class="auth-section">
        <div class="auth-box">
            <h1>Welcome Back</h1>
            <p class="subtitle">Sign in to your account</p>

            <?php echo showFlash(); ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" required placeholder="you@example.com">
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" required placeholder="Your password">
                </div>
                <button type="submit" class="btn btn-primary">Sign In</button>
            </form>

            <p class="auth-footer">
                Don't have an account? <a href="register.php">Create one</a>
            </p>
        </div>
    </section>
</main>

<?php require_once 'includes/footer.php'; ?>

