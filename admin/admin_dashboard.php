<?php
require_once __DIR__ . '/../functions.php';
require_once __DIR__ . '/db.php';

if (session_status() !== PHP_SESSION_ACTIVE) session_start();

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    redirect('../login.php');
}

// Ensure a CSRF token for this session
csrf_token();

// ── Stats ─────────────────────────────────────────────────────
$userCount     = $conn->query("SELECT COUNT(*) AS c FROM users")->fetch_assoc()['c'];
$propertyCount = $conn->query("SELECT COUNT(*) AS c FROM properties")->fetch_assoc()['c'];

// ── Recent rows ───────────────────────────────────────────────
$recentUsers      = $conn->query("SELECT id, username, created_at FROM users ORDER BY created_at DESC LIMIT 10");
$recentProperties = $conn->query("SELECT id, title, price, location, created_at FROM properties ORDER BY created_at DESC LIMIT 10");

$pageTitle = 'Admin Dashboard';
include __DIR__ . '/../header.php';
?>

<div class="container py-5">

    <h1 class="h2 fw-bold mb-4" style="color:var(--bremc-blue)">
        <i class="fa fa-shield-alt me-2"></i>Admin Dashboard
    </h1>

    <?php render_flash(); ?>

    <!-- Stats -->
    <div class="row g-3 mb-5">
        <div class="col-6 col-md-3">
            <div class="admin-stat-card">
                <h2><?php echo (int)$userCount; ?></h2>
                <p class="mb-0 small">Registered Users</p>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="admin-stat-card">
                <h2><?php echo (int)$propertyCount; ?></h2>
                <p class="mb-0 small">Total Properties</p>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="admin-stat-card">
                <i class="fa fa-plus fa-2x mb-1"></i>
                <p class="mb-0 small"><a href="../submit.php" class="text-white">Add Property</a></p>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="admin-stat-card">
                <i class="fa fa-user-plus fa-2x mb-1"></i>
                <p class="mb-0 small"><a href="add_user.php" class="text-white">Add User</a></p>
            </div>
        </div>
    </div>

    <div class="row g-4">

        <!-- Recent users -->
        <div class="col-lg-6">
            <div class="contact-card">
                <h4 class="fw-bold mb-3"><i class="fa fa-users me-2 text-muted"></i>Recent Users</h4>
                <div class="table-responsive">
                    <table class="table table-sm table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Username</th>
                                <th>Joined</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php while ($user = $recentUsers->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo (int)$user['id']; ?></td>
                            <td><?php echo e($user['username']); ?></td>
                            <td class="text-muted small"><?php echo e($user['created_at']); ?></td>
                            <td class="text-end">
                                <a href="edit_users.php?id=<?php echo (int)$user['id']; ?>"
                                   class="btn btn-sm btn-outline-primary me-1">Edit</a>
                                <!-- DELETE — POST form with CSRF -->
                                <form method="POST" action="delete_user.php"
                                      style="display:inline"
                                      onsubmit="return confirm('Delete user <?php echo e(addslashes($user['username'])); ?>?')">
                                    <?php echo csrf_field(); ?>
                                    <input type="hidden" name="id" value="<?php echo (int)$user['id']; ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Recent properties -->
        <div class="col-lg-6">
            <div class="contact-card">
                <h4 class="fw-bold mb-3"><i class="fa fa-building me-2 text-muted"></i>Recent Properties</h4>
                <div class="table-responsive">
                    <table class="table table-sm table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>Title</th>
                                <th>Price (K)</th>
                                <th>Location</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php while ($property = $recentProperties->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo e($property['title']); ?></td>
                            <td><?php echo number_format($property['price'], 2); ?></td>
                            <td class="text-muted small"><?php echo e($property['location']); ?></td>
                            <td class="text-end">
                                <a href="edit_property.php?id=<?php echo (int)$property['id']; ?>"
                                   class="btn btn-sm btn-outline-primary me-1">Edit</a>
                                <form method="POST" action="delete_property.php"
                                      style="display:inline"
                                      onsubmit="return confirm('Delete property: <?php echo e(addslashes($property['title'])); ?>?')">
                                    <?php echo csrf_field(); ?>
                                    <input type="hidden" name="id" value="<?php echo (int)$property['id']; ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div><!-- /.row -->

    <!-- Add user form -->
    <div class="contact-card mt-4">
        <h4 class="fw-bold mb-4"><i class="fa fa-user-plus me-2 text-muted"></i>Add New User</h4>
        <form method="POST" action="add_user.php">
            <?php echo csrf_field(); ?>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Username <span class="text-danger">*</span></label>
                    <input type="text" name="username" class="form-control" required placeholder="Username">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Email</label>
                    <input type="email" name="email" class="form-control" placeholder="email@example.com">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Phone</label>
                    <input type="text" name="phone" class="form-control" placeholder="+260…">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Password <span class="text-danger">*</span></label>
                    <input type="password" name="password" class="form-control" required placeholder="Password">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Confirm Password <span class="text-danger">*</span></label>
                    <input type="password" name="confirm_password" class="form-control" required placeholder="Confirm">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Role</label>
                    <select name="role" class="form-select">
                        <option value="user">User</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-bremc-primary">
                        <i class="fa fa-user-plus me-2"></i>Add User
                    </button>
                </div>
            </div>
        </form>
    </div>

</div>

<?php include __DIR__ . '/../footer.php'; ?>
