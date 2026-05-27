<?php
require_once 'functions.php';
$pageTitle       = 'About Us';
$metaDescription = 'Learn about Bremc Family Realtors – founded in 2019 to provide trusted real estate services to families across Zambia.';
include 'header.php';
?>

<!-- Hero -->
<div class="about-hero">
    <div class="container">
        <h1 class="display-5 fw-bold">About Bremc Family Realtors</h1>
        <p class="lead mt-2 mb-0">Helping families find their perfect home since 2019.</p>
    </div>
</div>

<!-- Our Story -->
<section class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="contact-card">
                <h2 class="section-title text-center d-block mx-auto mb-4">Our Story</h2>
                <p class="fs-5 text-muted lh-lg">
                    Bremc Family Realtors was founded in 2019 with a vision to provide exceptional real estate services
                    tailored to families and individuals. Over the years, we have built a reputation for trust,
                    transparency, and customer satisfaction. Our team of professionals is dedicated to making your
                    real estate journey smooth and stress-free.
                </p>
                <p class="fs-5 text-muted lh-lg mb-0">
                    Whether you're looking to buy, sell, or rent a property, our goal is to connect you with the best
                    opportunities in the market. With our experience and deep understanding of the Zambian real estate
                    industry, we ensure that every transaction is handled with professionalism and care.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Why Choose Us -->
<section class="container pb-5">
    <h2 class="section-title text-center d-block mx-auto mb-4">Why Choose Us</h2>
    <div class="row g-4 justify-content-center">
        <div class="col-sm-6 col-md-3 text-center">
            <div class="card service-card h-100 p-3">
                <div class="service-icon"><i class="fa fa-shield-alt"></i></div>
                <h5 class="fw-bold" style="color:var(--bremc-blue)">Trusted &amp; Transparent</h5>
                <p class="text-muted small">We operate with integrity and keep you informed at every step.</p>
            </div>
        </div>
        <div class="col-sm-6 col-md-3 text-center">
            <div class="card service-card h-100 p-3">
                <div class="service-icon"><i class="fa fa-map-marked-alt"></i></div>
                <h5 class="fw-bold" style="color:var(--bremc-blue)">Local Expertise</h5>
                <p class="text-muted small">Deep knowledge of the Zambian property market to guide your decisions.</p>
            </div>
        </div>
        <div class="col-sm-6 col-md-3 text-center">
            <div class="card service-card h-100 p-3">
                <div class="service-icon"><i class="fa fa-handshake"></i></div>
                <h5 class="fw-bold" style="color:var(--bremc-blue)">Family Focused</h5>
                <p class="text-muted small">We treat every client like family — your goals are our priority.</p>
            </div>
        </div>
        <div class="col-sm-6 col-md-3 text-center">
            <div class="card service-card h-100 p-3">
                <div class="service-icon"><i class="fa fa-headset"></i></div>
                <h5 class="fw-bold" style="color:var(--bremc-blue)">Dedicated Support</h5>
                <p class="text-muted small">Our team is always available to answer your questions and concerns.</p>
            </div>
        </div>
    </div>
</section>

<!-- Team -->
<section class="container pb-5">
    <h2 class="section-title text-center d-block mx-auto mb-5">Meet Our Team</h2>
    <div class="row g-4 justify-content-center">

        <div class="col-6 col-md-4 col-lg-3 text-center">
            <img src="img/team.jpg" alt="Enock Bwalya" class="team-photo mb-3">
            <h5 class="fw-bold mb-0">Enock Bwalya</h5>
            <p class="text-muted small">Founder &amp; CEO</p>
        </div>

        <div class="col-6 col-md-4 col-lg-3 text-center">
            <img src="img/team2.jpg" alt="Nathan Chilela" class="team-photo mb-3">
            <h5 class="fw-bold mb-0">Nathan Chilela</h5>
            <p class="text-muted small">Sales Agent</p>
        </div>

        <div class="col-6 col-md-4 col-lg-3 text-center">
            <img src="img/team3.jpg" alt="Musonda Chibambo" class="team-photo mb-3">
            <h5 class="fw-bold mb-0">Musonda Chibambo</h5>
            <p class="text-muted small">Property Agent</p>
        </div>

    </div>
</section>

<!-- CTA -->
<section class="py-5" style="background:linear-gradient(135deg,var(--bremc-blue),var(--bremc-teal))">
    <div class="container text-center text-white">
        <h2 class="fw-bold mb-2">Ready to find your next property?</h2>
        <p class="mb-4 opacity-75">Browse our current listings or reach out to our team today.</p>
        <a href="listings.php" class="btn btn-light me-3 px-4">Browse Listings</a>
        <a href="contact_us.php" class="btn btn-outline-light px-4">Contact Us</a>
    </div>
</section>

<?php include 'footer.php'; ?>
