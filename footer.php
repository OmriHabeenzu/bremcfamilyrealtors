</main><!-- /.page-content -->

<footer class="site-footer mt-5">
    <div class="container">
        <div class="row gy-4">

            <!-- Brand column -->
            <div class="col-md-4">
                <h5 class="text-white fw-bold mb-2">Bremc Family Realtors</h5>
                <p class="small">Helping families find their perfect home since 2019. Expert property lettings, sales, management and development in Zambia.</p>
                <div class="footer-social mt-3">
                    <a href="https://www.facebook.com/share/18dc4dSGvd/" target="_blank" rel="noopener" aria-label="Facebook">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="https://www.instagram.com/bremc_family_realtors" target="_blank" rel="noopener" aria-label="Instagram">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="https://youtube.com/@bremcfamilyrealtors5349" target="_blank" rel="noopener" aria-label="YouTube">
                        <i class="fab fa-youtube"></i>
                    </a>
                    <a href="https://www.tiktok.com/@bremcfamilyrealtors" target="_blank" rel="noopener" aria-label="TikTok">
                        <i class="fab fa-tiktok"></i>
                    </a>
                    <a href="https://wa.me/260977152018" target="_blank" rel="noopener" aria-label="WhatsApp">
                        <i class="fab fa-whatsapp"></i>
                    </a>
                </div>
            </div>

            <!-- Quick links -->
            <div class="col-md-2">
                <h6 class="text-white fw-semibold mb-3">Quick Links</h6>
                <ul class="list-unstyled small">
                    <li class="mb-1"><a href="index.php"><i class="fa fa-chevron-right me-1 small"></i>Home</a></li>
                    <li class="mb-1"><a href="listings.php"><i class="fa fa-chevron-right me-1 small"></i>Properties</a></li>
                    <li class="mb-1"><a href="about.php"><i class="fa fa-chevron-right me-1 small"></i>About Us</a></li>
                    <li class="mb-1"><a href="contact_us.php"><i class="fa fa-chevron-right me-1 small"></i>Contact Us</a></li>
                </ul>
            </div>

            <!-- Services -->
            <div class="col-md-2">
                <h6 class="text-white fw-semibold mb-3">Services</h6>
                <ul class="list-unstyled small">
                    <li class="mb-1"><a href="listings.php?type=sale"><i class="fa fa-chevron-right me-1 small"></i>Property Sales</a></li>
                    <li class="mb-1"><a href="listings.php?type=rent"><i class="fa fa-chevron-right me-1 small"></i>Rentals</a></li>
                    <li class="mb-1"><a href="contact_us.php"><i class="fa fa-chevron-right me-1 small"></i>Valuations</a></li>
                    <li class="mb-1"><a href="contact_us.php"><i class="fa fa-chevron-right me-1 small"></i>Consultation</a></li>
                </ul>
            </div>

            <!-- Contact info -->
            <div class="col-md-4">
                <h6 class="text-white fw-semibold mb-3">Contact Us</h6>
                <ul class="list-unstyled small">
                    <li class="mb-2">
                        <i class="fa fa-map-marker-alt me-2" style="color:var(--bremc-gold)"></i>
                        Chalala, Lusaka, Zambia
                    </li>
                    <li class="mb-2">
                        <i class="fa fa-phone me-2" style="color:var(--bremc-gold)"></i>
                        <a href="tel:+260977152018">+260 977 152 018</a>
                    </li>
                    <li class="mb-2">
                        <i class="fa fa-envelope me-2" style="color:var(--bremc-gold)"></i>
                        <a href="mailto:bremcfamilyrealtors@gmail.com">bremcfamilyrealtors@gmail.com</a>
                    </li>
                </ul>
            </div>

        </div><!-- /.row -->

        <hr class="footer-divider my-3">

        <div class="row align-items-center">
            <div class="col-md-6 small text-center text-md-start mb-2 mb-md-0">
                &copy; <?php echo date('Y'); ?> <strong class="text-white">Bremc Family Realtors</strong>. All Rights Reserved.
            </div>
            <div class="col-md-6 small text-center text-md-end">
                <a href="privacy_policy.php">Privacy Policy</a>
                <span class="mx-2">|</span>
                <a href="terms_of_service.php">Terms of Service</a>
                <span class="mx-2">|</span>
                <span>Designed by <a href="mailto:omrihabeenzu1@gmail.com">Omri Habeenzu</a></span>
            </div>
        </div>

    </div>
</footer>

<!-- Bootstrap 5.3 JS bundle (includes Popper) — loaded once, here -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Auto-dismiss flash alerts after 4 seconds -->
<script>
(function () {
    document.querySelectorAll('#flashContainer .alert').forEach(function (el) {
        setTimeout(function () {
            var bsAlert = bootstrap.Alert.getOrCreateInstance(el);
            bsAlert.close();
        }, 4000);
    });
})();
</script>

</body>
</html>
