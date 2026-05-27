<?php
require_once 'functions.php';
$pageTitle       = 'Contact Us';
$metaDescription = 'Get in touch with Bremc Family Realtors. Call, email, or visit us in Chalala, Lusaka.';
include 'header.php';
?>

<div class="container py-5">
    <h1 class="section-title text-center d-block mx-auto mb-4">Contact Us</h1>

    <?php render_flash(); ?>

    <div class="row g-4">

        <!-- Contact form -->
        <div class="col-lg-7">
            <div class="contact-card h-100">
                <h4 class="fw-bold mb-4"><i class="fa fa-paper-plane me-2 text-muted"></i>Send Us a Message</h4>
                <form method="POST" action="contact_process.php">
                    <?php echo csrf_field(); ?>
                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold">Your Name</label>
                        <input type="text" name="name" id="name" class="form-control"
                               placeholder="Enter your full name" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label fw-semibold">Your Email</label>
                        <input type="email" name="email" id="email" class="form-control"
                               placeholder="Enter your email address" required>
                    </div>
                    <div class="mb-4">
                        <label for="message" class="form-label fw-semibold">Your Message</label>
                        <textarea name="message" id="message" class="form-control" rows="5"
                                  placeholder="Write your message here…" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-bremc-primary w-100 py-2">
                        <i class="fa fa-paper-plane me-2"></i>Send Message
                    </button>
                </form>
            </div>
        </div>

        <!-- Contact info + map -->
        <div class="col-lg-5">
            <div class="contact-card mb-4">
                <h4 class="fw-bold mb-3"><i class="fa fa-address-card me-2 text-muted"></i>Our Details</h4>
                <ul class="list-unstyled">
                    <li class="mb-3 d-flex align-items-start gap-2">
                        <i class="fa fa-map-marker-alt mt-1" style="color:var(--bremc-teal);min-width:18px"></i>
                        <span>Chalala, Lusaka, Zambia</span>
                    </li>
                    <li class="mb-3 d-flex align-items-start gap-2">
                        <i class="fa fa-phone mt-1" style="color:var(--bremc-teal);min-width:18px"></i>
                        <a href="tel:+260977152018" class="text-dark">+260 977 152 018</a>
                    </li>
                    <li class="mb-3 d-flex align-items-start gap-2">
                        <i class="fa fa-envelope mt-1" style="color:var(--bremc-teal);min-width:18px"></i>
                        <a href="mailto:bremcfamilyrealtors@gmail.com" class="text-dark">bremcfamilyrealtors@gmail.com</a>
                    </li>
                    <li class="mb-3 d-flex align-items-start gap-2">
                        <i class="fab fa-whatsapp mt-1" style="color:#25d366;min-width:18px"></i>
                        <a href="https://wa.me/260977152018" target="_blank" rel="noopener" class="text-dark">Chat on WhatsApp</a>
                    </li>
                </ul>
            </div>

            <div class="rounded overflow-hidden" style="height:280px">
                <iframe
                    width="100%"
                    height="280"
                    style="border:0;"
                    loading="lazy"
                    allowfullscreen
                    referrerpolicy="no-referrer-when-downgrade"
                    title="Bremc Family Realtors Location"
                    src="https://www.google.com/maps/embed?pb=!1m17!1m12!1m3!1d3843.7376425829984!2d28.35922007512518!3d-15.552188985054899!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m2!1m1!2zMTXCsDMzJzA3LjkiUyAyOMKwMjEnNDIuNSJF!5e0!3m2!1sen!2szm!4v1743528052084!5m2!1sen!2szm">
                </iframe>
            </div>
        </div>

    </div>
</div>

<?php include 'footer.php'; ?>
