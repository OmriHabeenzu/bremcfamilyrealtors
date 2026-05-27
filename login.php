<?php
require_once 'functions.php';
require_once 'db.php';

if (session_status() !== PHP_SESSION_ACTIVE) session_start();

// Already logged in
if (isset($_SESSION['username'])) {
    redirect('index.php');
}

$errors = [];
$username_val = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    $username_val = clean($_POST['username'] ?? '');
    $password_raw = $_POST['password'] ?? '';

    if (empty($username_val)) $errors[] = 'Username is required.';
    if (empty($password_raw)) $errors[] = 'Password is required.';

    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
        $stmt->bind_param("s", $username_val);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if ($user && password_verify($password_raw, $user['password'])) {
            // Regenerate session ID to prevent fixation attacks
            session_regenerate_id(true);
            $_SESSION['username'] = $user['username'];
            $_SESSION['role']     = $user['role'];
            $_SESSION['user_id']  = $user['id'];

            $dest = ($user['role'] === 'admin') ? 'admin/admin_dashboard.php' : 'index.php';
            redirect($dest);
        } else {
            // Deliberate vague message — don't reveal whether username exists
            $errors[] = 'Invalid username or password.';
        }
    }
}

$pageTitle       = 'Login';
$metaDescription = 'Log in to your Bremc Family Realtors account.';
include 'header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5 col-lg-4">
            <div class="contact-card">
                <h1 class="h3 text-center fw-bold mb-4" style="color:var(--bremc-blue)">
                    <i class="fa fa-sign-in-alt me-2"></i>Login
                </h1>

                <?php render_flash(); ?>

                <?php if ($errors): ?>
                    <div class="alert alert-danger">
                        <?php foreach ($errors as $err): ?>
                            <div><?php echo e($err); ?></div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <?php echo csrf_field(); ?>

                    <div class="mb-3">
                        <label for="username" class="form-label fw-semibold">Username</label>
                        <input type="text" class="form-control" id="username" name="username"
                               value="<?php echo e($username_val); ?>"
                               autocomplete="username" required>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label fw-semibold">Password</label>
                        <input type="password" class="form-control" id="password" name="password"
                               autocomplete="current-password" required>
                    </div>

                    <button type="submit" class="btn btn-bremc-primary w-100 mb-3">
                        <i class="fa fa-sign-in-alt me-2"></i>Login
                    </button>

                    <div class="text-center small">
                        <a href="forgot_password.php">Forgot Password?</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
