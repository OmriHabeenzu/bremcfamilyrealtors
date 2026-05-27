<?php
require_once 'functions.php';
require_once 'db.php';

if (session_status() !== PHP_SESSION_ACTIVE) session_start();

// Auth check — must be logged in to see the form
if (!isset($_SESSION['user_id'])) {
    flash('Please log in to submit a property listing.', 'warning');
    redirect('login.php');
}

$pageTitle       = 'Submit a Property';
$metaDescription = 'List your property with Bremc Family Realtors. Fill in the details and upload photos to get started.';
include 'header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="contact-card">
                <h1 class="h3 fw-bold mb-4 text-center" style="color:var(--bremc-blue)">
                    <i class="fa fa-plus-circle me-2"></i>Submit a Property
                </h1>

                <?php render_flash(); ?>

                <form action="process_submit.php" method="POST" enctype="multipart/form-data" id="submitForm">
                    <?php echo csrf_field(); ?>

                    <div class="mb-3">
                        <label for="title" class="form-label fw-semibold">Property Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="title" name="title"
                               required maxlength="200" placeholder="e.g. 3-Bedroom House in Kabulonga">
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label fw-semibold">Description <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="description" name="description"
                                  rows="5" required
                                  placeholder="Describe the property, nearby amenities, condition, etc."></textarea>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="price" class="form-label fw-semibold">Price (K) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="price" name="price"
                                   required min="0" step="0.01" placeholder="e.g. 250000">
                        </div>
                        <div class="col-md-6">
                            <label for="rooms" class="form-label fw-semibold">Number of Rooms <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="rooms" name="rooms"
                                   required min="1" placeholder="e.g. 3">
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="location" class="form-label fw-semibold">Location <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="location" name="location"
                                   required maxlength="200" placeholder="e.g. Lusaka, Kabulonga">
                        </div>
                        <div class="col-md-6">
                            <label for="type" class="form-label fw-semibold">Listing Type <span class="text-danger">*</span></label>
                            <select class="form-select" id="type" name="type" required>
                                <option value="sale">For Sale</option>
                                <option value="rent">For Rent</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Property Images <span class="text-danger">*</span>
                            <span class="text-muted fw-normal">(JPG/PNG, max 5 MB each, up to 5 images)</span>
                        </label>
                        <input type="file" class="form-control" id="images" name="images[]"
                               accept="image/jpeg,image/png" multiple required>
                        <!-- Image preview strip -->
                        <div class="d-flex flex-wrap gap-2 mt-2" id="imagePreviews"></div>
                    </div>

                    <div class="mb-4">
                        <label for="video_link" class="form-label fw-semibold">YouTube Video Link <span class="text-muted fw-normal">(optional)</span></label>
                        <input type="url" class="form-control" id="video_link" name="video_link"
                               placeholder="https://www.youtube.com/watch?v=…">
                        <div class="form-text">Paste a YouTube URL to add a video tour of the property.</div>
                    </div>

                    <button type="submit" class="btn btn-bremc-primary w-100 py-2 fs-5">
                        <i class="fa fa-upload me-2"></i>Submit Property
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    var input    = document.getElementById('images');
    var previews = document.getElementById('imagePreviews');

    input.addEventListener('change', function () {
        previews.innerHTML = '';
        var files = Array.from(input.files).slice(0, 5);
        files.forEach(function (file) {
            if (!file.type.match(/^image\/(jpeg|png)$/)) return;
            var reader = new FileReader();
            reader.onload = function (ev) {
                var img = document.createElement('img');
                img.src = ev.target.result;
                img.style.cssText = 'width:90px;height:70px;object-fit:cover;border-radius:6px;border:2px solid #ddd;';
                img.alt = file.name;
                previews.appendChild(img);
            };
            reader.readAsDataURL(file);
        });
    });
})();
</script>

<?php include 'footer.php'; ?>
