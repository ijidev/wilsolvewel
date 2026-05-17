<?php
require_once __DIR__ . '/../config.php';
secure_session_start();

if (empty($_SESSION['admin_id'])) {
    $redirect = urlencode($_SERVER['REQUEST_URI']);
    header("Location: " . (defined('ADMIN_ROOT') ? ADMIN_ROOT : '../admin/') . "login.php?redirect=$redirect");
    exit;
}

// Regenerate session ID periodically to prevent fixation
if (empty($_SESSION['last_regenerated']) || $_SESSION['last_regenerated'] < (time() - 1800)) {
    session_regenerate_id(true);
    $_SESSION['last_regenerated'] = time();
}

// Enforce CSRF check for POST requests on admin pages (exclude AJAX)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($_GET['ajax_action'])) {
    $token = $_POST['csrf_token'] ?? '';
    if (!verify_csrf_token($token)) {
        http_response_code(403);
        die('Invalid or expired CSRF token. Please reload the page and try again.');
    }
}
