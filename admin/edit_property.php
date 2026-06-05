<?php
require_once __DIR__ . '/../functions.php';
require_once __DIR__ . '/db.php';

if (session_status() !== PHP_SESSION_ACTIVE) session_start();

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    redirect('../login.php');
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) {
    flash('No property specified.', 'danger');
    redirect('admin_dashboard.php');
}

// Fetch existing property
$stmt = $conn->prepare("SELECT * FROM properties WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$property = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$property) {
    flash('Property not found.', 'danger');
    redirect('admin_dashboard.php');
}

$errors = [];

// Handle update submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    $title       = clean($_POST['title'] ?? '');
    $description = clean($_POST['description'] ?? '');
    $location    = clean($_POST['location'] ?? '');
    $price       = filter_var($_POST['price'] ?? '', FILTER_VALIDATE_FLOAT);
    $rooms       = filter_var($_POST['rooms'] ?? '', FILTER_VALIDATE_INT);
    $type        = in_array($_POST['type'] ?? '', ['sale', 'rent']) ? $_POST['type'] : '';
    $video_url   = filter_var(trim($_POST['video_url'] ?? ''), FILTER_VALIDATE_URL) ?: null;

    if (!$title || !$description || $price === false || !$location || $rooms === false || !$type) {
        $errors[] = 'All required fields must be filled in correctly.';
    }

    // Handle new images if uploaded
    $newImagePaths = [];
    if (!empty($_FILES['images']['name'][0])) {
        $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png'];
        $target_dir = __DIR__ . '/../property_images/';
        for ($i = 0; $i < count($_FILES['images']['name']); $i++) {
            if ($_FILES['images']['error'][$i] !== UPLOAD_ERR_OK) continue;
            if ($_FILES['images']['size'][$i] > 5_000_000) { $errors[] = 'An image exceeds 5 MB.'; continue; }
            $mime = mime_content_type($_FILES['images']['tmp_name'][$i]);
            if (!array_key_exists($mime, $allowed)) { $errors[] = 'Only JPG/PNG images are accepted.'; continue; }
            if (getimagesize($_FILES['images']['tmp_name'][$i]) === false) continue;
            $newName = uniqid('img_', true) . '.' . $allowed[$mime];
            if (move_uploaded_file($_FILES['images']['tmp_name'][$i], $target_dir . $newName)) {
                $newImagePaths[] = $newName;
            }
        }
    }

    if (empty($errors)) {
        if ($newImagePaths) {
            $cover      = $newImagePaths[0];
            $others     = implode(',', array_slice($newImagePaths, 1));
            $stmt = $conn->prepare(
                "UPDATE properties SET title=?, description=?, price=?, location=?, rooms=?, type=?, video_url=?, cover_image=?, other_images=? WHERE id=?"
            );
            $stmt->bind_param("ssdsssssi", $title, $description, $price, $location, $rooms, $type, $video_url, $cover, $others, $id);
        } else {
            $stmt = $conn->prepare(
                "UPDATE properties SET title=?, description=?, price=?, location=?, rooms=?, type=?, video_url=? WHERE id=?"
            );
            $stmt->bind_param("ssdssssi", $title, $description, $price, $location, $rooms, $type, $video_url, $id);
        }

        if ($stmt->execute()) {
            $stmt->close();
            flash('Property updated successfully.', 'success');
            redirect('admin_dashboard.php');
        } else {
            $errors[] = 'Database error: ' . $stmt->error;
            $stmt->close();
        }
    }
}

$pageTitle = 'Edit Property';
include __DIR__ . '/../header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="contact-card">
                <h1 class="h3 fw-bold mb-4" style="color:var(--bremc-blue)">
                    <i class="fa fa-edit me-2"></i>Edit Property
                </h1>

                <?php if ($errors): ?>
                    <div class="alert alert-danger">
                        <?php foreach ($errors as $err): ?><div><?php echo e($err); ?></div><?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Property Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="title"
                               value="<?php echo e($property['title']); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Description <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="description" rows="5" required><?php echo e($property['description']); ?></textarea>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Price (K) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="price"
                                   value="<?php echo e($property['price']); ?>" step="0.01" min="0" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Rooms <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="rooms"
                                   value="<?php echo (int)$property['rooms']; ?>" min="1" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Type <span class="text-danger">*</span></label>
                            <select class="form-select" name="type" required>
                                <option value="sale" <?php echo $property['type'] === 'sale' ? 'selected' : ''; ?>>For Sale</option>
                                <option value="rent" <?php echo $property['type'] === 'rent' ? 'selected' : ''; ?>>For Rent</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Location <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="location"
                               value="<?php echo e($property['location']); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Video URL <span class="text-muted fw-normal">(optional)</span></label>
                        <input type="url" class="form-control" name="video_url"
                               value="<?php echo e($property['video_url'] ?? ''); ?>"
                               placeholder="https://www.youtube.com/watch?v=…">
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Replace Images <span class="text-muted fw-normal">(leave blank to keep current)</span></label>
                        <input type="file" class="form-control" name="images[]"
                               accept="image/jpeg,image/png" multiple>
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
