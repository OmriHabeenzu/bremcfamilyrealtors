<?php
$pageTitle       = 'Home';
$metaDescription = 'Bremc Family Realtors – find your dream home in Zambia. Browse property sales, rentals, and professional real estate services.';
require_once 'db.php';
include 'header.php';

// Fetch 3 most recent properties
$stmt = $conn->prepare("SELECT * FROM properties ORDER BY id DESC LIMIT 3");
$stmt->execute();
$recentResult = $stmt->get_result();
$recentProperties = $recentResult->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!-- ── Hero Carousel ─────────────────────────────────────────── -->
<div id="heroCarousel" class="carousel slide hero-carousel" data-bs-ride="carousel" data-bs-interval="4000">
    <div class="carousel-indicators">
        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
    </div>
    <div class="carousel-inner">
        <div class="carousel-item active">
            <img src="img/slide.png" class="d-block w-100" alt="Find Your Dream Home" loading="eager">
            <div class="carousel-caption d-none d-md-block">
                <h2 class="fw-bold">Find Your Dream Home</h2>
                <p>Expert real estate services tailored for families across Zambia.</p>
                <a href="listings.php" class="btn btn-bremc-primary me-2">Browse Properties</a>
                <a href="contact_us.php" class="btn btn-outline-light">Contact Us</a>
            </div>
        </div>
        <div class="carousel-item">
            <img src="img/slide2.png" class="d-block w-100" alt="Property Sales & Rentals" loading="lazy">
            <div class="carousel-caption d-none d-md-block">
                <h2 class="fw-bold">Sales &amp; Rentals</h2>
                <p>Buy, sell, or rent — we make every transaction smooth and transparent.</p>
                <a href="listings.php?type=sale" class="btn btn-bremc-primary me-2">For Sale</a>
                <a href="listings.php?type=rent" class="btn btn-outline-light">For Rent</a>
            </div>
        </div>
        <div class="carousel-item">
            <img src="img/slide3.png" class="d-block w-100" alt="Professional Property Management" loading="lazy">
            <div class="carousel-caption d-none d-md-block">
                <h2 class="fw-bold">Professional Management</h2>
                <p>Comprehensive property management solutions for landlords &amp; investors.</p>
                <a href="about.php" class="btn btn-bremc-primary">Learn More</a>
            </div>
        </div>
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
    </button>
</div>

<!-- ── Services Section ──────────────────────────────────────── -->
<section class="container py-5">
    <h2 class="section-title text-center d-block mx-auto mb-4">Our Services</h2>
    <div class="row g-4">

        <div class="col-sm-6 col-lg-3">
            <div class="card service-card h-100 text-center p-3">
                <div class="card-body">
                    <div class="service-icon"><i class="fa fa-key"></i></div>
                    <h5 class="card-title">Property Lettings &amp; Sales</h5>
                    <p class="card-text text-muted">We match the right tenants and buyers to every property with care and expertise.</p>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card service-card h-100 text-center p-3">
                <div class="card-body">
                    <div class="service-icon"><i class="fa fa-tools"></i></div>
                    <h5 class="card-title">Property Management</h5>
                    <p class="card-text text-muted">Comprehensive solutions to help landlords maintain and grow their asset portfolio.</p>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card service-card h-100 text-center p-3">
                <div class="card-body">
                    <div class="service-icon"><i class="fa fa-hard-hat"></i></div>
                    <h5 class="card-title">Property Development</h5>
                    <p class="card-text text-muted">From planning and design to project delivery — we bring your vision to life.</p>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card service-card h-100 text-center p-3">
                <div class="card-body">
                    <div class="service-icon"><i class="fa fa-chart-line"></i></div>
                    <h5 class="card-title">Consultation &amp; Valuations</h5>
                    <p class="card-text text-muted">Expert advice and accurate market valuations to guide your investment decisions.</p>
                </div>
            </div>
        </div>

    </div>
</section>

<!-- ── Recent Properties ─────────────────────────────────────── -->
<section class="container pb-5">
    <h2 class="section-title text-center d-block mx-auto mb-4">Recent Properties</h2>
    <div class="row g-4">
        <?php if ($recentProperties): ?>
            <?php foreach ($recentProperties as $row): ?>
            <div class="col-md-4">
                <div class="card property-card h-100 position-relative">
                    <span class="property-badge <?php echo $row['type'] === 'rent' ? 'badge-rent' : 'badge-sale'; ?>">
                        <?php echo $row['type'] === 'rent' ? 'For Rent' : 'For Sale'; ?>
                    </span>
                    <img src="images/<?php echo e($row['cover_image']); ?>"
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
            <div class="col-12 text-center text-muted">No properties listed yet.</div>
        <?php endif; ?>
    </div>
    <div class="text-center mt-4">
        <a href="listings.php" class="btn btn-outline-secondary px-4">View All Properties</a>
    </div>
</section>

<!-- ── Testimonials ──────────────────────────────────────────── -->
<section class="testimonials-section">
    <div class="container">
        <h2 class="section-title text-center d-block mx-auto mb-4" style="color:#fff;">What Our Clients Say</h2>
        <div id="testimonialsCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="4000">
            <div class="carousel-inner">

                <div class="carousel-item active">
                    <div class="testimonial-card mx-auto" style="max-width:680px;">
                        <p>"Bremc Family Realtors helped me find my dream home! Highly recommend their services."</p>
                        <div class="testimonial-author">— Gilbert Ngulube</div>
                    </div>
                </div>

                <div class="carousel-item">
                    <div class="testimonial-card mx-auto" style="max-width:680px;">
                        <p>"The team was incredibly professional and made the buying process completely seamless."</p>
                        <div class="testimonial-author">— Omri Habeenzu</div>
                    </div>
                </div>

                <div class="carousel-item">
                    <div class="testimonial-card mx-auto" style="max-width:680px;">
                        <p>"I appreciate their attention to detail and dedication to finding exactly the right property."</p>
                        <div class="testimonial-author">— Nathan Chilela</div>
                    </div>
                </div>

                <div class="carousel-item">
                    <div class="testimonial-card mx-auto" style="max-width:680px;">
                        <p>"Exceptional service! They really go above and beyond for every client."</p>
                        <div class="testimonial-author">— Musonda Chibambo</div>
                    </div>
                </div>

            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#testimonialsCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#testimonialsCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>
    </div>
</section>

<!-- ── Contact Us ─────────────────────────────────────────────── -->
<section class="container py-5">
    <h2 class="section-title text-center d-block mx-auto mb-4">Get In Touch</h2>
    <?php render_flash(); ?>
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="contact-card">
                <form action="contact_process.php" method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label">Your Name</label>
                            <input type="text" class="form-control" id="name" name="name" required placeholder="Enter your name">
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label">Your Email</label>
                            <input type="email" class="form-control" id="email" name="email" required placeholder="Enter your email">
                        </div>
                        <div class="col-12">
                            <label for="message" class="form-label">Message</label>
                            <textarea class="form-control" id="message" name="message" rows="4" required placeholder="Type your message here…"></textarea>
                        </div>
                        <div class="col-12 text-center">
                            <button type="submit" class="btn btn-bremc-primary px-5">
                                <i class="fa fa-paper-plane me-2"></i>Send Message
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>
