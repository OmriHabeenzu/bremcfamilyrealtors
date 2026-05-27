<?php
/**
 * header.php — Site-wide page header partial
 *
 * Pages set these variables BEFORE including this file:
 *   $pageTitle       (string)  — tab / <title> text, defaults to site name
 *   $metaDescription (string)  — <meta name="description"> content
 *
 * This file outputs everything from <!DOCTYPE html> through
 * the closing </nav> tag, then leaves the page body open.
 * footer.php closes </body></html>.
 */

require_once __DIR__ . '/functions.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Ensure a CSRF token exists for this session
csrf_token();

$currentPage = basename($_SERVER['PHP_SELF']);

function isActive(string $page): string {
    global $currentPage;
    return $currentPage === $page ? 'active' : '';
}

$siteTitle       = 'Bremc Family Realtors';
$fullTitle       = isset($pageTitle) ? e($pageTitle) . ' | ' . $siteTitle : $siteTitle;
$metaDesc        = isset($metaDescription) ? e($metaDescription) : 'Bremc Family Realtors – expert property lettings, sales, management and development in Zambia.';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo $metaDesc; ?>">
    <meta property="og:title" content="<?php echo $fullTitle; ?>">
    <meta property="og:description" content="<?php echo $metaDesc; ?>">
    <meta property="og:type" content="website">
    <title><?php echo $fullTitle; ?></title>

    <!-- Bootstrap 5.3 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Site stylesheet -->
    <link rel="stylesheet" href="<?php echo str_repeat('../', substr_count($currentPage, '/') + (strpos($_SERVER['PHP_SELF'], '/admin/') !== false ? 1 : 0)); ?>css/styles.css">
</head>
<body>

<!-- Flash message container (rendered by render_flash() in individual pages) -->
<div class="flash-container" id="flashContainer"></div>

<header>
    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container">

            <a class="navbar-brand d-flex align-items-center gap-2" href="<?php echo (strpos($_SERVER['PHP_SELF'], '/admin/') !== false) ? '../index.php' : 'index.php'; ?>">
                <img src="<?php echo (strpos($_SERVER['PHP_SELF'], '/admin/') !== false) ? '../' : ''; ?>img/company_logo.jpg"
                     alt="Bremc Family Realtors Logo"
                     class="navbar-brand-logo">
                <span class="fw-semibold">Bremc Family Realtors</span>
            </a>

            <button class="navbar-toggler" type="button"
                    data-bs-toggle="collapse" data-bs-target="#navbarNav"
                    aria-controls="navbarNav" aria-expanded="false"
                    aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <?php
                $base = (strpos($_SERVER['PHP_SELF'], '/admin/') !== false) ? '../' : '';
                ?>
                <ul class="navbar-nav ms-auto align-items-lg-center">
                    <li class="nav-item">
                        <a href="<?php echo $base; ?>index.php"
                           class="nav-link <?php echo isActive('index.php'); ?>">
                            <i class="fa fa-home me-1"></i>Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo $base; ?>listings.php"
                           class="nav-link <?php echo isActive('listings.php'); ?>">
                            <i class="fa fa-building me-1"></i>Properties
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo $base; ?>about.php"
                           class="nav-link <?php echo isActive('about.php'); ?>">
                            <i class="fa fa-users me-1"></i>About Us
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo $base; ?>contact_us.php"
                           class="nav-link <?php echo isActive('contact_us.php'); ?>">
                            <i class="fa fa-envelope me-1"></i>Contact
                        </a>
                    </li>

                    <?php if (isset($_SESSION['username'])): ?>
                        <li class="nav-item">
                            <a href="<?php echo $base; ?>submit.php"
                               class="nav-link <?php echo isActive('submit.php'); ?>">
                                <i class="fa fa-plus-circle me-1"></i>Submit Property
                            </a>
                        </li>
                        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                        <li class="nav-item">
                            <a href="<?php echo $base; ?>admin/admin_dashboard.php"
                               class="nav-link">
                                <i class="fa fa-shield-alt me-1"></i>Admin
                            </a>
                        </li>
                        <?php endif; ?>
                        <li class="nav-item">
                            <a href="<?php echo $base; ?>logout.php"
                               class="nav-link text-warning">
                                <i class="fa fa-sign-out-alt me-1"></i>Logout
                            </a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a href="<?php echo $base; ?>login.php"
                               class="nav-link <?php echo isActive('login.php'); ?>">
                                <i class="fa fa-sign-in-alt me-1"></i>Login
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo $base; ?>register.php"
                               class="nav-link <?php echo isActive('register.php'); ?>">
                                <i class="fa fa-user-plus me-1"></i>Register
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>

                <form class="d-flex ms-lg-3 mt-2 mt-lg-0" action="<?php echo $base; ?>search.php" method="GET" role="search">
                    <input class="form-control form-control-sm me-2"
                           type="search" name="query"
                           placeholder="Search properties…"
                           aria-label="Search properties"
                           value="<?php echo isset($_GET['query']) ? e($_GET['query']) : ''; ?>">
                    <button class="btn btn-search btn-sm" type="submit">
                        <i class="fa fa-search"></i>
                    </button>
                </form>
            </div>

        </div>
    </nav>
</header>

<main class="page-content">
