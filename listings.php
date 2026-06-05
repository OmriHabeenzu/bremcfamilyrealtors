<?php
require_once 'db.php';
require_once 'functions.php';

// ── Filter inputs ────────────────────────────────────────────
$type     = in_array($_GET['type'] ?? '', ['sale', 'rent']) ? $_GET['type'] : '';
$location = clean($_GET['location'] ?? '');
$minPrice = isset($_GET['min_price']) && is_numeric($_GET['min_price']) ? (float)$_GET['min_price'] : '';
$maxPrice = isset($_GET['max_price']) && is_numeric($_GET['max_price']) ? (float)$_GET['max_price'] : '';
$rooms    = isset($_GET['rooms']) && ctype_digit($_GET['rooms']) ? (int)$_GET['rooms'] : '';

// ── Pagination ───────────────────────────────────────────────
$perPage     = 9;
$currentPage = max(1, (int)($_GET['page'] ?? 1));
$offset      = ($currentPage - 1) * $perPage;

// ── Build WHERE clause ───────────────────────────────────────
$where  = [];
$params = [];
$types  = '';

if ($type) {
    $where[]  = 'type = ?';
    $params[] = $type;
    $types   .= 's';
}
if ($location) {
    $where[]  = 'location LIKE ?';
    $params[] = '%' . $location . '%';
    $types   .= 's';
}
if ($minPrice !== '') {
    $where[]  = 'price >= ?';
    $params[] = $minPrice;
    $types   .= 'd';
}
if ($maxPrice !== '') {
    $where[]  = 'price <= ?';
    $params[] = $maxPrice;
    $types   .= 'd';
}
if ($rooms !== '') {
    $where[]  = 'rooms >= ?';
    $params[] = $rooms;
    $types   .= 'i';
}

$whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

// ── Count total (for pagination) ─────────────────────────────
$countSql  = "SELECT COUNT(*) AS total FROM properties $whereSql";
$countStmt = $conn->prepare($countSql);
if ($types) $countStmt->bind_param($types, ...$params);
$countStmt->execute();
$totalRows  = $countStmt->get_result()->fetch_assoc()['total'];
$totalPages = (int)ceil($totalRows / $perPage);
$countStmt->close();

// ── Fetch page of properties ─────────────────────────────────
$sql      = "SELECT * FROM properties $whereSql ORDER BY id DESC LIMIT ? OFFSET ?";
$stmt     = $conn->prepare($sql);
$allTypes = $types . 'ii';
$allParams = array_merge($params, [$perPage, $offset]);
$stmt->bind_param($allTypes, ...$allParams);
$stmt->execute();
$properties = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// ── Build query string helper for pagination links ───────────
function buildQuery(array $extra = []): string {
    $q = array_merge($_GET, $extra);
    unset($q['page']); // page is managed separately
    $base = $q ? '?' . http_build_query($q) . '&' : '?';
    return $base;
}

$pageTitle       = 'Property Listings';
$metaDescription = 'Browse all property listings by Bremc Family Realtors – houses for sale and rent in Zambia.';
include 'header.php';
?>

<div class="container py-5">
    <h1 class="section-title text-center d-block mx-auto mb-4">Property Listings</h1>

    <!-- ── Filter Panel ─────────────────────────────────────── -->
    <form method="GET" action="listings.php" class="filter-panel">
        <div class="row g-2 align-items-end">

            <div class="col-6 col-md-2">
                <label class="form-label small fw-semibold">Type</label>
                <select name="type" class="form-select form-select-sm">
                    <option value="">All</option>
                    <option value="sale" <?php echo $type === 'sale' ? 'selected' : ''; ?>>For Sale</option>
                    <option value="rent" <?php echo $type === 'rent' ? 'selected' : ''; ?>>For Rent</option>
                </select>
            </div>

            <div class="col-6 col-md-3">
                <label class="form-label small fw-semibold">Location</label>
                <input type="text" name="location" class="form-control form-control-sm"
                       placeholder="e.g. Lusaka" value="<?php echo e($location); ?>">
            </div>

            <div class="col-6 col-md-2">
                <label class="form-label small fw-semibold">Min Price (K)</label>
                <input type="number" name="min_price" class="form-control form-control-sm"
                       placeholder="0" value="<?php echo $minPrice !== '' ? e($minPrice) : ''; ?>">
            </div>

            <div class="col-6 col-md-2">
                <label class="form-label small fw-semibold">Max Price (K)</label>
                <input type="number" name="max_price" class="form-control form-control-sm"
                       placeholder="Any" value="<?php echo $maxPrice !== '' ? e($maxPrice) : ''; ?>">
            </div>

            <div class="col-6 col-md-1">
                <label class="form-label small fw-semibold">Min Rooms</label>
                <input type="number" name="rooms" class="form-control form-control-sm"
                       min="1" placeholder="Any" value="<?php echo $rooms !== '' ? $rooms : ''; ?>">
            </div>

            <div class="col-6 col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-bremc-primary btn-sm flex-fill">
                    <i class="fa fa-filter me-1"></i>Filter
                </button>
                <a href="listings.php" class="btn btn-outline-secondary btn-sm flex-fill">Clear</a>
            </div>

        </div>
    </form>

    <!-- ── Result count ─────────────────────────────────────── -->
    <p class="text-muted small mb-3">
        Showing
        <strong><?php echo min($offset + 1, $totalRows); ?>–<?php echo min($offset + $perPage, $totalRows); ?></strong>
        of <strong><?php echo $totalRows; ?></strong>
        propert<?php echo $totalRows === 1 ? 'y' : 'ies'; ?>
        <?php if ($type || $location || $minPrice !== '' || $maxPrice !== '' || $rooms !== ''): ?>
            <a href="listings.php" class="ms-2 small">(clear filters)</a>
        <?php endif; ?>
    </p>

    <!-- ── Property Grid ─────────────────────────────────────── -->
    <div class="row g-4">
        <?php if ($properties): ?>
            <?php foreach ($properties as $row): ?>
            <div class="col-sm-6 col-lg-4">
                <div class="card property-card h-100 position-relative">
                    <span class="property-badge <?php echo $row['type'] === 'rent' ? 'badge-rent' : 'badge-sale'; ?>">
                        <?php echo $row['type'] === 'rent' ? 'For Rent' : 'For Sale'; ?>
                    </span>
                    <img src="img.php?f=<?php echo e($row['cover_image']); ?>"
                         class="card-img-top"
                         alt="<?php echo e($row['title']); ?>"
                         loading="lazy">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title fw-bold"><?php echo e($row['title']); ?></h5>
                        <p class="property-price">K<?php echo number_format($row['price'], 2); ?></p>
                        <p class="property-meta mb-1">
                            <i class="fa fa-map-marker-alt"></i> <?php echo e($row['location']); ?>
                        </p>
                        <p class="property-meta mb-3">
                            <i class="fa fa-bed"></i> <?php echo (int)$row['rooms']; ?> Rooms
                        </p>
                        <a href="property.php?id=<?php echo (int)$row['id']; ?>"
                           class="btn btn-bremc-primary mt-auto">
                            View Property
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12 text-center py-5">
                <i class="fa fa-search fa-3x text-muted mb-3 d-block"></i>
                <p class="text-muted fs-5">No properties found matching your criteria.</p>
                <a href="listings.php" class="btn btn-outline-secondary">Clear Filters</a>
            </div>
        <?php endif; ?>
    </div>

    <!-- ── Pagination ────────────────────────────────────────── -->
    <?php if ($totalPages > 1): ?>
    <nav class="mt-5" aria-label="Listings pagination">
        <ul class="pagination justify-content-center">
            <li class="page-item <?php echo $currentPage <= 1 ? 'disabled' : ''; ?>">
                <a class="page-link" href="<?php echo buildQuery(); ?>page=<?php echo $currentPage - 1; ?>" aria-label="Previous">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>

            <?php for ($p = max(1, $currentPage - 2); $p <= min($totalPages, $currentPage + 2); $p++): ?>
            <li class="page-item <?php echo $p === $currentPage ? 'active' : ''; ?>">
                <a class="page-link" href="<?php echo buildQuery(); ?>page=<?php echo $p; ?>"><?php echo $p; ?></a>
            </li>
            <?php endfor; ?>

            <li class="page-item <?php echo $currentPage >= $totalPages ? 'disabled' : ''; ?>">
                <a class="page-link" href="<?php echo buildQuery(); ?>page=<?php echo $currentPage + 1; ?>" aria-label="Next">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
        </ul>
    </nav>
    <?php endif; ?>

</div>

<?php include 'footer.php'; ?>
