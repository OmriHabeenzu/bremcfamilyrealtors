<?php
require_once 'db.php';
require_once 'functions.php';

if (session_status() !== PHP_SESSION_ACTIVE) session_start();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) redirect('listings.php');

$stmt = $conn->prepare("
    SELECT p.*,
           COALESCE(u.username, 'Bremc Realtors') AS poster_name,
           COALESCE(u.email, 'bremcfamilyrealtors@gmail.com') AS poster_email,
           COALESCE(u.phone, '260977152018') AS poster_phone
    FROM properties p
    LEFT JOIN users u ON p.user_id = u.id
    WHERE p.id = ?
");
$stmt->bind_param("i", $id);
$stmt->execute();
$property = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$property) {
    flash('Property not found.', 'danger');
    redirect('listings.php');
}

function extractYouTubeId(string $url): string {
    parse_str(parse_url($url, PHP_URL_QUERY) ?? '', $query);
    return $query['v'] ?? '';
}

function timeAgo(string $date): string {
    $diff = time() - strtotime($date);
    $days = floor($diff / 86400);
    if ($days < 1)  return 'Posted today';
    if ($days === 1) return 'Posted 1 day ago';
    return "Posted {$days} days ago";
}

$video_id     = extractYouTubeId($property['video_url'] ?? '');
$scheme       = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$property_url = $scheme . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

csrf_token();

$pageTitle       = e($property['title']);
$metaDescription = e(mb_substr(strip_tags($property['description']), 0, 155));
include 'header.php';

$isRent  = $property['type'] === 'rent';
$images  = array_values(array_filter(array_map('trim', explode(',', $property['other_images'] ?? ''))));
if (empty($images)) $images = [$property['cover_image']];
?>

<!-- ══════════════════════════════════════════════════════════
     HERO BANNER — title, price, key info
════════════════════════════════════════════════════════════ -->
<div class="prop-hero">
    <div class="container">
        <div class="d-flex flex-wrap align-items-start justify-content-between gap-3">

            <!-- Left: title + meta -->
            <div>
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="prop-type-badge <?php echo $isRent ? 'prop-badge-rent' : 'prop-badge-sale'; ?>">
                        <?php echo $isRent ? 'For Rent' : 'For Sale'; ?>
                    </span>
                    <span class="prop-posted"><?php echo timeAgo($property['created_at'] ?? ''); ?></span>
                </div>
                <h1 class="prop-hero-title"><?php echo e($property['title']); ?></h1>
                <p class="prop-hero-location">
                    <i class="fa fa-map-marker-alt me-1"></i><?php echo e($property['location']); ?>
                </p>
            </div>

            <!-- Right: price -->
            <div class="prop-hero-price-box">
                <div class="prop-hero-price">K<?php echo number_format($property['price'], 2); ?></div>
                <div class="prop-hero-price-label"><?php echo $isRent ? 'per month' : 'asking price'; ?></div>
            </div>

        </div>
    </div>
</div>

<!-- ══════════════════════════════════════════════════════════
     MAIN CONTENT
════════════════════════════════════════════════════════════ -->
<div class="container py-5">
    <?php render_flash(); ?>

    <div class="row g-4 align-items-start">

        <!-- ─────────────────────────────────────────────────
             LEFT COLUMN  (images + details + description)
        ───────────────────────────────────────────────────── -->
        <div class="col-lg-8">

            <!-- IMAGE CAROUSEL -->
            <div id="propertyCarousel" class="carousel slide prop-carousel" data-bs-ride="false">
                <div class="carousel-inner">
                    <?php foreach ($images as $i => $img): ?>
                    <div class="carousel-item <?php echo $i === 0 ? 'active' : ''; ?>">
                        <img src="images/<?php echo e(trim($img)); ?>"
                             class="d-block w-100"
                             alt="<?php echo e($property['title']); ?> photo <?php echo $i + 1; ?>"
                             loading="<?php echo $i === 0 ? 'eager' : 'lazy'; ?>">
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php if (count($images) > 1): ?>
                <button class="carousel-control-prev" type="button" data-bs-target="#propertyCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#propertyCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon"></span>
                    <span class="visually-hidden">Next</span>
                </button>
                <?php endif; ?>
            </div>

            <!-- THUMBNAIL STRIP -->
            <?php if (count($images) > 1): ?>
            <div class="prop-thumbs" id="propThumbs">
                <?php foreach ($images as $i => $img): ?>
                <img src="images/<?php echo e(trim($img)); ?>"
                     class="prop-thumb <?php echo $i === 0 ? 'active' : ''; ?>"
                     data-bs-target="#propertyCarousel"
                     data-bs-slide-to="<?php echo $i; ?>"
                     loading="lazy"
                     alt="Photo <?php echo $i + 1; ?>">
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <!-- ── PROPERTY SPECS GRID ───────────────────── -->
            <div class="prop-specs-card">
                <h3 class="prop-section-title">Property Overview</h3>
                <div class="prop-specs-grid">

                    <div class="prop-spec-item">
                        <div class="prop-spec-icon"><i class="fa fa-tag"></i></div>
                        <div class="prop-spec-label">Listing Type</div>
                        <div class="prop-spec-value"><?php echo $isRent ? 'For Rent' : 'For Sale'; ?></div>
                    </div>

                    <div class="prop-spec-item">
                        <div class="prop-spec-icon"><i class="fa fa-bed"></i></div>
                        <div class="prop-spec-label">Bedrooms</div>
                        <div class="prop-spec-value"><?php echo (int)$property['rooms']; ?></div>
                    </div>

                    <div class="prop-spec-item">
                        <div class="prop-spec-icon"><i class="fa fa-map-marker-alt"></i></div>
                        <div class="prop-spec-label">Location</div>
                        <div class="prop-spec-value"><?php echo e($property['location']); ?></div>
                    </div>

                    <div class="prop-spec-item">
                        <div class="prop-spec-icon"><i class="fa fa-coins"></i></div>
                        <div class="prop-spec-label">Price</div>
                        <div class="prop-spec-value">K<?php echo number_format($property['price'], 2); ?></div>
                    </div>

                    <div class="prop-spec-item">
                        <div class="prop-spec-icon"><i class="fa fa-calendar-alt"></i></div>
                        <div class="prop-spec-label">Listed</div>
                        <div class="prop-spec-value"><?php echo timeAgo($property['created_at'] ?? ''); ?></div>
                    </div>

                    <div class="prop-spec-item">
                        <div class="prop-spec-icon"><i class="fa fa-user-tie"></i></div>
                        <div class="prop-spec-label">Agent</div>
                        <div class="prop-spec-value"><?php echo e($property['poster_name']); ?></div>
                    </div>

                </div>
            </div>

            <!-- ── DESCRIPTION ───────────────────────────── -->
            <div class="prop-description-card">
                <h3 class="prop-section-title">About This Property</h3>
                <div class="prop-description-body">
                    <?php echo nl2br(e($property['description'])); ?>
                </div>
            </div>

            <!-- ── YOUTUBE VIDEO ────────────────────────── -->
            <?php if ($video_id): ?>
            <div class="prop-video-card">
                <h3 class="prop-section-title">
                    <i class="fab fa-youtube text-danger me-2"></i>Virtual Tour
                </h3>
                <div class="ratio ratio-16x9 rounded-3 overflow-hidden">
                    <iframe src="https://www.youtube.com/embed/<?php echo e($video_id); ?>"
                            title="Property virtual tour"
                            allowfullscreen
                            loading="lazy"></iframe>
                </div>
            </div>
            <?php endif; ?>

            <!-- Back button -->
            <div class="mt-4">
                <a href="listings.php" class="back-button">
                    <i class="fa fa-arrow-left me-2"></i>Back to Listings
                </a>
            </div>

        </div><!-- /col-lg-8 -->

        <!-- ─────────────────────────────────────────────────
             RIGHT COLUMN  (sticky sidebar)
        ───────────────────────────────────────────────────── -->
        <div class="col-lg-4">
            <div class="prop-sidebar">

                <!-- ── AGENT INFO ──────────────────────── -->
                <div class="prop-agent-card mb-4">
                    <div class="prop-agent-header">
                        <div class="prop-agent-avatar">
                            <i class="fa fa-user-tie"></i>
                        </div>
                        <div>
                            <div class="prop-agent-name"><?php echo e($property['poster_name']); ?></div>
                            <div class="prop-agent-role">Listing Agent</div>
                        </div>
                    </div>
                    <?php if ($property['poster_phone']): ?>
                    <a href="tel:<?php echo preg_replace('/[^0-9+]/', '', $property['poster_phone']); ?>"
                       class="btn prop-btn-call w-100 mb-2">
                        <i class="fa fa-phone me-2"></i>
                        Call <?php echo e($property['poster_phone']); ?>
                    </a>
                    <?php endif; ?>
                    <button class="btn prop-btn-whatsapp w-100" onclick="openWhatsApp(event)">
                        <i class="fab fa-whatsapp me-2"></i>WhatsApp
                    </button>
                </div>

                <!-- ── EMAIL ENQUIRY FORM ───────────────── -->
                <div class="prop-enquiry-card">
                    <h5 class="prop-enquiry-title">
                        <i class="fa fa-envelope me-2"></i>Send Enquiry
                    </h5>

                    <form action="contact_owner_process.php" method="POST">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="property_title" value="<?php echo e($property['title']); ?>">
                        <input type="hidden" name="to_email"       value="<?php echo e($property['poster_email']); ?>">

                        <div class="mb-3">
                            <input type="text" class="form-control prop-input" name="sender_name"
                                   required placeholder="Your full name"
                                   value="<?php echo isset($_SESSION['username']) ? e($_SESSION['username']) : ''; ?>">
                        </div>
                        <div class="mb-3">
                            <input type="email" class="form-control prop-input" name="sender_email"
                                   required placeholder="Your email address">
                        </div>
                        <div class="mb-3">
                            <textarea class="form-control prop-input" name="message"
                                      rows="5" required
                                      placeholder="I am interested in this property…"><?php
                                echo "Hello, I am interested in the property titled '" . e($property['title']) . "'.\n\nProperty link: " . e($property_url);
                            ?></textarea>
                        </div>
                        <button type="submit" class="btn prop-btn-enquiry w-100">
                            <i class="fa fa-paper-plane me-2"></i>Send Enquiry
                        </button>
                    </form>

                    <!-- Divider -->
                    <div class="prop-or-divider"><span>or contact directly</span></div>

                    <!-- WhatsApp form -->
                    <form id="whatsappForm" onsubmit="openWhatsApp(event)">
                        <div class="mb-2">
                            <textarea class="form-control prop-input" id="wa_message" rows="3"
                                      placeholder="Type a WhatsApp message…"><?php
                                echo "Hello, I am interested in the property titled '" . e($property['title']) . "'.";
                            ?></textarea>
                        </div>
                        <button type="submit" class="btn prop-btn-whatsapp w-100">
                            <i class="fab fa-whatsapp me-2"></i>Send via WhatsApp
                        </button>
                    </form>
                </div>

                <!-- ── SHARE ────────────────────────────── -->
                <div class="prop-share-card mt-3">
                    <div class="prop-share-label">Share this listing</div>
                    <div class="d-flex gap-2 justify-content-center mt-2">
                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode($property_url); ?>"
                           target="_blank" rel="noopener"
                           class="prop-share-btn prop-share-fb" title="Share on Facebook">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode($property_url); ?>&text=<?php echo urlencode('Check out this property: ' . $property['title']); ?>"
                           target="_blank" rel="noopener"
                           class="prop-share-btn prop-share-tw" title="Share on Twitter">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="https://wa.me/?text=<?php echo urlencode($property['title'] . ' - ' . $property_url); ?>"
                           target="_blank" rel="noopener"
                           class="prop-share-btn prop-share-wa" title="Share on WhatsApp">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                        <button class="prop-share-btn prop-share-copy" title="Copy link"
                                onclick="copyLink('<?php echo e($property_url); ?>', this)">
                            <i class="fa fa-link"></i>
                        </button>
                    </div>
                </div>

            </div><!-- /prop-sidebar -->
        </div><!-- /col-lg-4 -->

    </div><!-- /.row -->
</div><!-- /.container -->

<style>
/* ── Property Detail Page Styles ─────────────────────────────── */

/* Hero banner */
.prop-hero {
    background: linear-gradient(135deg, var(--bremc-blue) 0%, #1a3a7a 100%);
    padding: 2.2rem 0 2rem;
    color: #fff;
}
.prop-hero-title {
    font-size: clamp(1.4rem, 3vw, 2.2rem);
    font-weight: 800;
    line-height: 1.25;
    margin-bottom: .4rem;
    color: #fff;
}
.prop-hero-location {
    font-size: 1rem;
    color: rgba(255,255,255,.8);
    margin: 0;
}
.prop-type-badge {
    font-size: .72rem;
    font-weight: 700;
    letter-spacing: .07em;
    text-transform: uppercase;
    padding: .3em .85em;
    border-radius: 20px;
}
.prop-badge-sale { background: var(--bremc-teal);   color: #fff; }
.prop-badge-rent { background: var(--bremc-orange);  color: #fff; }
.prop-posted { font-size: .8rem; color: rgba(255,255,255,.6); }

.prop-hero-price-box {
    text-align: right;
    background: rgba(255,255,255,.12);
    border: 1px solid rgba(255,255,255,.25);
    border-radius: 12px;
    padding: 1rem 1.4rem;
    min-width: 180px;
}
.prop-hero-price {
    font-size: clamp(1.5rem, 3vw, 2rem);
    font-weight: 800;
    color: var(--bremc-gold);
    line-height: 1.1;
}
.prop-hero-price-label {
    font-size: .8rem;
    color: rgba(255,255,255,.65);
    text-transform: uppercase;
    letter-spacing: .06em;
}

/* Carousel */
.prop-carousel .carousel-inner {
    border-radius: 14px;
    overflow: hidden;
    box-shadow: 0 8px 30px rgba(0,0,0,.18);
}
.prop-carousel .carousel-item img {
    width: 100%;
    height: 400px;
    object-fit: cover;
}
.prop-carousel .carousel-control-prev,
.prop-carousel .carousel-control-next {
    width: 48px;
    height: 48px;
    top: 50%;
    transform: translateY(-50%);
    bottom: auto;
    background: rgba(0,0,0,.55);
    border-radius: 50%;
    margin: 0 12px;
    opacity: 1;
    transition: background .2s;
}
.prop-carousel .carousel-control-prev:hover,
.prop-carousel .carousel-control-next:hover {
    background: var(--bremc-teal);
}

/* Thumbnails */
.prop-thumbs {
    display: flex;
    gap: 8px;
    margin-top: 10px;
    flex-wrap: wrap;
}
.prop-thumb {
    width: 72px;
    height: 52px;
    object-fit: cover;
    border-radius: 8px;
    cursor: pointer;
    border: 3px solid transparent;
    transition: border-color .2s, transform .2s;
    opacity: .75;
}
.prop-thumb:hover,
.prop-thumb.active {
    border-color: var(--bremc-teal);
    opacity: 1;
    transform: scale(1.06);
}

/* Section cards (specs / description / video) */
.prop-specs-card,
.prop-description-card,
.prop-video-card {
    background: #fff;
    border-radius: 14px;
    box-shadow: 0 2px 14px rgba(0,0,0,.07);
    padding: 1.75rem;
    margin-top: 1.5rem;
}

.prop-section-title {
    font-size: 1.15rem;
    font-weight: 700;
    color: var(--bremc-blue);
    margin-bottom: 1.25rem;
    padding-bottom: .6rem;
    border-bottom: 2px solid #f0f2f5;
}

/* Specs grid */
.prop-specs-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1rem;
}
@media (max-width: 576px) {
    .prop-specs-grid { grid-template-columns: repeat(2, 1fr); }
}
.prop-spec-item {
    background: #f8faff;
    border: 1px solid #e8edf5;
    border-radius: 10px;
    padding: 1rem .9rem;
    text-align: center;
    transition: box-shadow .2s, transform .2s;
}
.prop-spec-item:hover {
    box-shadow: 0 4px 16px rgba(13,43,107,.1);
    transform: translateY(-2px);
}
.prop-spec-icon {
    font-size: 1.5rem;
    color: var(--bremc-teal);
    margin-bottom: .4rem;
}
.prop-spec-label {
    font-size: .72rem;
    text-transform: uppercase;
    letter-spacing: .06em;
    color: #999;
    margin-bottom: .25rem;
}
.prop-spec-value {
    font-size: .95rem;
    font-weight: 700;
    color: var(--bremc-blue);
    word-break: break-word;
}

/* Description */
.prop-description-body {
    font-size: 1rem;
    color: #4a4a5a;
    line-height: 1.8;
}

/* ── Sidebar ───────────────────────────────────────────────── */
.prop-sidebar {
    position: sticky;
    top: 85px;
}

/* Agent card */
.prop-agent-card {
    background: var(--bremc-blue);
    color: #fff;
    border-radius: 14px;
    padding: 1.4rem;
    box-shadow: 0 6px 20px rgba(13,43,107,.25);
}
.prop-agent-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1rem;
}
.prop-agent-avatar {
    width: 52px;
    height: 52px;
    border-radius: 50%;
    background: rgba(255,255,255,.15);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.4rem;
    flex-shrink: 0;
}
.prop-agent-name {
    font-weight: 700;
    font-size: 1rem;
}
.prop-agent-role {
    font-size: .78rem;
    color: rgba(255,255,255,.65);
}
.prop-btn-call {
    background: rgba(255,255,255,.15);
    border: 1px solid rgba(255,255,255,.3);
    color: #fff;
    border-radius: 8px;
    font-weight: 600;
    font-size: .9rem;
    transition: background .2s;
}
.prop-btn-call:hover {
    background: rgba(255,255,255,.28);
    color: #fff;
}
.prop-btn-whatsapp {
    background: #25d366;
    border: none;
    color: #fff;
    border-radius: 8px;
    font-weight: 600;
    font-size: .9rem;
    transition: background .2s;
}
.prop-btn-whatsapp:hover {
    background: #1aa74e;
    color: #fff;
}

/* Enquiry form card */
.prop-enquiry-card {
    background: #fff;
    border-radius: 14px;
    box-shadow: 0 2px 16px rgba(0,0,0,.08);
    padding: 1.5rem;
}
.prop-enquiry-title {
    font-weight: 700;
    color: var(--bremc-blue);
    margin-bottom: 1.1rem;
    font-size: 1rem;
}
.prop-input {
    border-radius: 8px;
    border: 1px solid #dde3ee;
    font-size: .9rem;
    background: #f9fafc;
}
.prop-input:focus {
    border-color: var(--bremc-teal);
    box-shadow: 0 0 0 3px rgba(0,131,116,.12);
    background: #fff;
}
.prop-btn-enquiry {
    background: var(--bremc-teal);
    border: none;
    color: #fff;
    border-radius: 8px;
    font-weight: 600;
    padding: .65rem;
    transition: background .2s;
}
.prop-btn-enquiry:hover {
    background: var(--bremc-blue);
    color: #fff;
}

/* OR divider */
.prop-or-divider {
    display: flex;
    align-items: center;
    text-align: center;
    color: #aaa;
    font-size: .8rem;
    margin: 1rem 0;
}
.prop-or-divider::before,
.prop-or-divider::after {
    content: '';
    flex: 1;
    border-bottom: 1px solid #e5e5e5;
}
.prop-or-divider span { padding: 0 .75rem; }

/* Share card */
.prop-share-card {
    background: #fff;
    border-radius: 12px;
    padding: 1rem;
    box-shadow: 0 2px 10px rgba(0,0,0,.06);
    text-align: center;
}
.prop-share-label { font-size: .78rem; color: #888; text-transform: uppercase; letter-spacing: .06em; }
.prop-share-btn {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    border: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
    color: #fff;
    cursor: pointer;
    transition: transform .2s, opacity .2s;
    text-decoration: none;
}
.prop-share-btn:hover { transform: scale(1.12); opacity: .9; color: #fff; }
.prop-share-fb   { background: #1877f2; }
.prop-share-tw   { background: #1da1f2; }
.prop-share-wa   { background: #25d366; }
.prop-share-copy { background: #6c757d; }
</style>

<script>
(function () {
    var phone        = '<?php echo preg_replace('/[^0-9]/', '', $property['poster_phone']); ?>';
    var propertyLink = '<?php echo e($property_url); ?>';

    /* WhatsApp opener */
    window.openWhatsApp = function (e) {
        if (e && e.preventDefault) e.preventDefault();
        if (!phone) { alert('Phone number not available for this listing.'); return; }
        var waBox = document.getElementById('wa_message');
        var msg   = (waBox ? waBox.value.trim() : '') || 'Hello, I am interested in this property.';
        var full  = msg + '\n\nProperty link: ' + propertyLink;
        window.open('https://wa.me/' + phone + '?text=' + encodeURIComponent(full), '_blank');
    };

    /* Copy link button */
    window.copyLink = function (url, btn) {
        navigator.clipboard.writeText(url).then(function () {
            btn.innerHTML = '<i class="fa fa-check"></i>';
            setTimeout(function () { btn.innerHTML = '<i class="fa fa-link"></i>'; }, 2000);
        });
    };

    /* Thumbnail ↔ carousel sync */
    var carousel = document.getElementById('propertyCarousel');
    var thumbs   = document.querySelectorAll('.prop-thumb');

    if (carousel) {
        carousel.addEventListener('slide.bs.carousel', function (ev) {
            thumbs.forEach(function (t) { t.classList.remove('active'); });
            if (thumbs[ev.to]) thumbs[ev.to].classList.add('active');
        });
    }

    thumbs.forEach(function (thumb) {
        thumb.addEventListener('click', function () {
            thumbs.forEach(function (t) { t.classList.remove('active'); });
            thumb.classList.add('active');
        });
    });
})();
</script>

<?php include 'footer.php'; ?>
