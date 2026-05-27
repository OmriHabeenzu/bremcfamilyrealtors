<?php
require_once 'functions.php';

if (session_status() !== PHP_SESSION_ACTIVE) session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('index.php');
}

// CSRF
csrf_verify();

// Sanitise & validate
$property_title = clean($_POST['property_title'] ?? '');
$to_email       = valid_email($_POST['to_email'] ?? '');
$sender_name    = clean($_POST['sender_name'] ?? '');
$sender_email   = valid_email($_POST['sender_email'] ?? '');
$message        = clean($_POST['message'] ?? '');

if (!$to_email || !$sender_name || !$sender_email || empty($message)) {
    flash('Please fill in all required fields with a valid email address.', 'warning');
    redirect($_SERVER['HTTP_REFERER'] ?? 'listings.php');
}

$subject = "New Inquiry on Property: {$property_title}";

// Use a fixed From address — never a user-supplied one
$headers  = 'From: website@bremcfamilyrealtors.net' . "\r\n";
$headers .= 'Reply-To: ' . $sender_email . "\r\n";
$headers .= 'Content-Type: text/plain; charset=UTF-8' . "\r\n";

$body  = "Hello,\r\n\r\n";
$body .= "You have received a new message regarding the property titled \"{$property_title}\".\r\n\r\n";
$body .= "From:    {$sender_name}\r\n";
$body .= "Email:   {$sender_email}\r\n\r\n";
$body .= "Message:\r\n{$message}\r\n\r\n";
$body .= "Regards,\r\nBremc Family Realtors Website";

if (mail($to_email, $subject, $body, $headers)) {
    flash('Your message has been sent successfully. The agent will be in touch soon!', 'success');
} else {
    flash('Message could not be delivered. Please try again or contact us directly.', 'danger');
}

// Go back to the property page if possible
$ref = $_SERVER['HTTP_REFERER'] ?? 'listings.php';
redirect($ref);
