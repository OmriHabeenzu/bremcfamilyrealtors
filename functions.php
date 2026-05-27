<?php
/**
 * functions.php — Shared security and utility helpers
 * Include this file wherever session + CSRF are needed.
 */

// ─── CSRF ────────────────────────────────────────────────────────────────────

/**
 * Generate (or reuse) a CSRF token stored in the session.
 */
function csrf_token(): string {
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Render a hidden CSRF input field.
 */
function csrf_field(): string {
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(csrf_token()) . '">';
}

/**
 * Verify the submitted CSRF token.
 * Dies with 403 if the token is missing or wrong.
 */
function csrf_verify(): void {
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    $submitted = $_POST['csrf_token'] ?? '';
    $expected  = $_SESSION['csrf_token'] ?? '';
    if (!$submitted || !hash_equals($expected, $submitted)) {
        http_response_code(403);
        die('Invalid request. Please go back and try again.');
    }
}

// ─── Input / Output helpers ──────────────────────────────────────────────────

/**
 * Escape a value for safe HTML output.
 */
function e(string $value): string {
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Sanitize a plain-text string (trim + strip tags).
 */
function clean(string $value): string {
    return trim(strip_tags($value));
}

/**
 * Validate and sanitise an email address.
 * Returns the clean email or FALSE.
 */
function valid_email(string $email): string|false {
    $email = filter_var(trim($email), FILTER_VALIDATE_EMAIL);
    // Also strip any \r or \n to prevent header injection
    return $email ? str_replace(["\r", "\n"], '', $email) : false;
}

// ─── Redirect helper ─────────────────────────────────────────────────────────

function redirect(string $url): void {
    header('Location: ' . $url);
    exit();
}

// ─── Flash messages ──────────────────────────────────────────────────────────

/**
 * Store a one-time message in the session.
 * Type: 'success' | 'danger' | 'warning' | 'info'
 */
function flash(string $message, string $type = 'success'): void {
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    $_SESSION['flash'] = ['message' => $message, 'type' => $type];
}

/**
 * Render and clear any stored flash message.
 */
function render_flash(): void {
    if (!empty($_SESSION['flash'])) {
        $f = $_SESSION['flash'];
        unset($_SESSION['flash']);
        echo '<div class="alert alert-' . e($f['type']) . ' alert-dismissible fade show" role="alert">'
           . e($f['message'])
           . '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>'
           . '</div>';
    }
}
