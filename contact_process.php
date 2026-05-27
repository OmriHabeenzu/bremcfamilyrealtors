<?php
require_once 'functions.php';
require_once 'db.php';

if (session_status() !== PHP_SESSION_ACTIVE) session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('contact_us.php');
}

// ── CSRF check ───────────────────────────────────────────────
csrf_verify();

// ── Sanitise & validate inputs ───────────────────────────────
$name    = clean($_POST['name'] ?? '');
$email   = valid_email($_POST['email'] ?? '');
$message = clean($_POST['message'] ?? '');

if (empty($name) || !$email || empty($message)) {
    flash('Please fill in all fields with a valid email address.', 'warning');
    redirect('contact_us.php');
}

// ── Build and send email (safe from header injection) ────────
$to      = 'bremcfamilyrealtors@gmail.com';
$subject = 'New Contact Form Message from ' . $name;

// Headers use only the validated/stripped email
$headers  = 'From: website@bremcfamilyrealtors.net' . "\r\n";
$headers .= 'Reply-To: ' . $email . "\r\n";
$headers .= 'Content-Type: text/plain; charset=UTF-8' . "\r\n";

$body  = "You have received a new message from the contact form.\r\n\r\n";
$body .= "Name:    {$name}\r\n";
$body .= "Email:   {$email}\r\n";
$body .= "Message:\r\n{$message}\r\n";

if (mail($to, $subject, $body, $headers)) {
    flash('Thank you! Your message has been sent successfully.', 'success');
} else {
    flash('Sorry, we could not send your message right now. Please try again or call us directly.', 'danger');
}

redirect('contact_us.php');
