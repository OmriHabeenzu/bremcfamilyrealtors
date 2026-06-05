<?php
require_once 'db.php';
require_once 'functions.php';

$searchQuery = clean($_GET['query'] ?? '');
$properties  = [];

if ($searchQuery !== '') {
    $stmt = $conn->prepare("
        SELECT * FROM properties
        WHERE title    LIKE CONCAT('%', ?, '%')
           OR location LIKE CONCAT('%', ?, '%')
        ORDER BY id DESC
        LIMIT 50
    ");
    $stmt->bind_param("ss", $searchQuery, $searchQuery);
    $stmt->execute();
    $properties = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}

$pageTitle       = $searchQuery ? 'Search: ' . $searchQuery : 'Property Search';
$metaDescription = 'Search for properties by title or location on Bremc Family Realtors.';
include 'header.php';
?>

<div class="container py-5">

    <!-- Search bar -->
    <div class="row justify-content-center mb-4">
        <div class="col-md-8">
            <form method="GET" action="search.php" class="d-flex gap-2" role="search">
                <input class="form-control"
                       type="search"
                       name="query"
                       placeholder="Search by title or location…"
                       value="<?php echo e($searchQuery); ?>"
                       aria-label="Search properties">
                <button class="btn btn-bremc-primary" type="submit">
                    <i class="fa fa-search me-1"></i>Search
                </button>
            </form>
        </div>
    </div>

    <?php if ($searchQuery !== ''): ?>

        <h2 class="h4 mb-3">
            <?php if ($properties): ?>
                <?php echo count($properties); ?> result<?php echo count($properties) !== 1 ? 's' : ''; ?>
                for &ldquo;<strong><?php echo e($searchQuery); ?></strong>&rdquo;
            <?php else: ?>
                No results for &ldquo;<strong><?php echo e($searchQuery); ?></strong>&rdquo;
            <?php endif; ?>
        </h2>

        <?php if ($properties): ?>
        <div class="row g-4">
            <?php foreach ($properties as $property): ?>
            <div class="col-sm-6 col-lg-4">
                <div class="card property-card h-100 position-relative">
                    <span class="property-badge <?php echo $property['type'] === 'rent' ? 'badge-rent' : 'badge-sale'; ?>">
                        <?php echo $property['type'] === 'rent' ? 'For Rent' : 'For Sale'; ?>
                    </span>
                    <img src="img.php?f=<?php echo e($property['cover_image']); ?>"
                         class="card-img-top"
                         alt="<?php echo e($property['title']); ?>"
                         loading="lazy">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title fw-bold"><?php echo e($property['title']); ?></h5>
                        <p class="property-price">K<?php echo number_format($property['price'], 2); ?></p>
                        <p class="property-meta mb-1">
                            <i class="fa fa-map-marker-alt"></i> <?php echo e($property['location']); ?>
                        </p>
                        <p class="property-meta mb-3">
                            <i class="fa fa-bed"></i> <?php echo (int)$property['rooms']; ?> Rooms
                        </p>
                        <a href="property.php?id=<?php echo (int)$property['id']; ?>"
                           class="btn btn-bremc-primary mt-auto">
                            View Property
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="text-center py-5">
            <i class="fa fa-search fa-3x text-muted mb-3 d-block"></i>
            <p class="text-muted">Try a different keyword or <a href="listings.php">browse all properties</a>.</p>
        </div>
        <?php endif; ?>

    <?php else: ?>
        <div class="text-center py-5">
            <i class="fa fa-home fa-3x text-muted mb-3 d-block"></i>
            <p class="text-muted">Enter a location or property name above to search.</p>
            <a href="listings.php" class="btn btn-bremc-primary">Browse All Properties</a>
        </div>
    <?php endif; ?>

</div>

<?php include 'footer.php'; ?>
