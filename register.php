<?php
require_once 'functions.php';
require_once 'db.php';

if (session_status() !== PHP_SESSION_ACTIVE) session_start();

// Already logged-in users don't need to register
if (isset($_SESSION['username'])) {
    redirect('index.php');
}

$errors = [];
$username_val = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ── CSRF check ──────────────────────────────────────────
    csrf_verify();

    $username_val = clean($_POST['username'] ?? '');
    $password_raw = $_POST['password'] ?? '';
    $confirm_pass = $_POST['confirm_password'] ?? '';

    // ── Validation ───────────────────────────────────────────
    if (empty($username_val)) {
        $errors[] = 'Username is required.';
    } elseif (strlen($username_val) < 3 || strlen($username_val) > 30) {
        $errors[] = 'Username must be between 3 and 30 characters.';
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username_val)) {
        $errors[] = 'Username may only contain letters, numbers and underscores.';
    }

    if (empty($password_raw)) {
        $errors[] = 'Password is required.';
    } elseif (strlen($password_raw) < 8) {
        $errors[] = 'Password must be at least 8 characters.';
    }

    if ($password_raw !== $confirm_pass) {
        $errors[] = 'Passwords do not match.';
    }

    // ── Check for existing username (prepared statement) ─────
    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->bind_param("s", $username_val);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $errors[] = 'That username is already taken. Please choose another.';
        }
        $stmt->close();
    }

    // ── Insert new user ───────────────────────────────────────
    if (empty($errors)) {
        $hashed = password_hash($password_raw, PASSWORD_DEFAULT);
        $stmt   = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        $stmt->bind_param("ss", $username_val, $hashed);
        if ($stmt->execute()) {
            flash('Registration successful! You can now log in.', 'success');
            redirect('login.php');
        } else {
            $errors[] = 'Registration failed. Please try again.';
        }
        $stmt->close();
    }
}

$pageTitle       = 'Register';
$metaDescription = 'Create a Bremc Family Realtors account to submit and manage property listings.';
include 'header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="contact-card">
                <h1 class="h3 text-center mb-4 fw-bold" style="color:var(--bremc-blue)">
                    <i class="fa fa-user-plus me-2"></i>Create Account
                </h1>

                <?php if ($errors): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0 ps-3">
                            <?php foreach ($errors as $err): ?>
                                <li><?php echo e($err); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <?php echo csrf_field(); ?>

                    <div class="mb-3">
                        <label for="username" class="form-label fw-semibold">Username</label>
                        <input type="text" class="form-control" id="username" name="username"
                               value="<?php echo e($username_val); ?>"
                               autocomplete="username" required
                               placeholder="letters, numbers, underscores">
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label fw-semibold">Password</label>
                        <input type="password" class="form-control" id="password" name="password"
                               autocomplete="new-password" required
                               placeholder="Min. 8 characters">
                    </div>

                    <div class="mb-4">
                        <label for="confirm_password" class="form-label fw-semibold">Confirm Password</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password"
                               autocomplete="new-password" required
                               placeholder="Repeat your password">
                    </div>

                    <button type="submit" class="btn btn-bremc-primary w-100">
                        <i class="fa fa-user-plus me-2"></i>Register
                    </button>
                </form>

                <p class="text-center mt-3 small">
                    Already have an account? <a href="login.php">Log in here</a>
                </p>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
