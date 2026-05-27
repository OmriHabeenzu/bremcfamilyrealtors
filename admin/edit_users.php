<?php
require_once __DIR__ . '/../functions.php';
require_once __DIR__ . '/db.php';

if (session_status() !== PHP_SESSION_ACTIVE) session_start();

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    redirect('../login.php');
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) {
    flash('No user specified.', 'danger');
    redirect('admin_dashboard.php');
}

// Fetch user
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$user) {
    flash('User not found.', 'danger');
    redirect('admin_dashboard.php');
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    $username = clean($_POST['username'] ?? '');
    $email    = valid_email($_POST['email'] ?? '') ?: '';
    $phone    = preg_replace('/[^0-9+\- ]/', '', $_POST['phone'] ?? '');

    if (empty($username)) $errors[] = 'Username is required.';

    if (empty($errors)) {
        $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, phone = ? WHERE id = ?");
        $stmt->bind_param("sssi", $username, $email, $phone, $id);

        if ($stmt->execute()) {
            $stmt->close();
            flash('User updated successfully.', 'success');
            redirect('admin_dashboard.php');
        } else {
            $errors[] = 'Database error: ' . $stmt->error;
            $stmt->close();
        }
    }
}

$pageTitle = 'Edit User';
include __DIR__ . '/../header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="contact-card">
                <h1 class="h3 fw-bold mb-4" style="color:var(--bremc-blue)">
                    <i class="fa fa-user-edit me-2"></i>Edit User: <?php echo e($user['username']); ?>
                </h1>

                <?php if ($errors): ?>
                    <div class="alert alert-danger">
                        <?php foreach ($errors as $err): ?><div><?php echo e($err); ?></div><?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <?php echo csrf_field(); ?>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Username <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="username"
                               value="<?php echo e($user['username']); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email</label>
                        <input type="email" class="form-control" name="email"
                               value="<?php echo e($user['email'] ?? ''); ?>">
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Phone</label>
                        <input type="text" class="form-control" name="phone"
                               value="<?php echo e($user['phone'] ?? ''); ?>"
                               placeholder="+260…">
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-bremc-primary">
                            <i class="fa fa-save me-2"></i>Save Changes
                        </button>
                        <a href="admin_dashboard.php" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../footer.php'; ?>
