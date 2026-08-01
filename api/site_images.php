<?php
require_once __DIR__ . '/../config.php';
$conn = get_db_connection();

function api_json($data, $code = 200) {
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

// Public read: list of image overrides keyed by "{page}:{original_src}" -> override url
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $map = [];
    $res = $conn->query("SELECT image_key, override_url FROM site_images WHERE override_url IS NOT NULL AND override_url != ''");
    if ($res) {
        while ($row = $res->fetch_assoc()) $map[$row['image_key']] = $row['override_url'];
    }
    api_json(['images' => $map]);
}

// Mutations require an authenticated admin session
secure_session_start();
if (empty($_SESSION['admin_id'])) {
    api_json(['status' => 'error', 'message' => 'Unauthorized'], 401);
}
if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
    api_json(['status' => 'error', 'message' => 'Invalid security token'], 403);
}

$admin_id = (int)$_SESSION['admin_id'];
$action = $_POST['action'] ?? '';
$image_key = trim($_POST['image_key'] ?? '');

if ($image_key === '') {
    api_json(['status' => 'error', 'message' => 'Missing image key'], 422);
}

$page_file = '';
$original_src = '';
if (strpos($image_key, ':') !== false) {
    list($page_file, $original_src) = explode(':', $image_key, 2);
}

function upsert_override($conn, $image_key, $page_file, $original_src, $url) {
    $stmt = $conn->prepare("INSERT INTO site_images (image_key, page_file, original_src, override_url) VALUES (?, ?, ?, ?)
                            ON DUPLICATE KEY UPDATE override_url = VALUES(override_url), page_file = VALUES(page_file), original_src = VALUES(original_src)");
    $stmt->bind_param("ssss", $image_key, $page_file, $original_src, $url);
    $stmt->execute();
    $stmt->close();
}

function valid_url($url) {
    if ($url === '') return true;
    if (preg_match('#^https?://#i', $url)) return true;
    if (preg_match('#^uploads/#', $url)) return true;
    return false;
}

switch ($action) {
    case 'save':
        $override_url = trim($_POST['override_url'] ?? '');
        if ($override_url === '') {
            api_json(['status' => 'error', 'message' => 'Enter an image URL or upload a file.'], 422);
        }
        if (!valid_url($override_url)) {
            api_json(['status' => 'error', 'message' => 'Invalid image URL. Use http(s):// or an uploads/ path.'], 422);
        }
        upsert_override($conn, $image_key, $page_file, $original_src, $override_url);
        log_audit($conn, 'Update', 'SiteImage', 'Admin', $admin_id, "Set image for $image_key to $override_url");
        api_json(['status' => 'success', 'message' => 'Image updated.', 'url' => $override_url]);

    case 'delete':
        $stmt = $conn->prepare("UPDATE site_images SET override_url = NULL WHERE image_key = ?");
        $stmt->bind_param("s", $image_key);
        $stmt->execute();
        $stmt->close();
        log_audit($conn, 'Delete', 'SiteImage', 'Admin', $admin_id, "Reset image for $image_key");
        api_json(['status' => 'success', 'message' => 'Image reset to original.']);

    case 'upload':
        if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
            api_json(['status' => 'error', 'message' => 'No file received.'], 422);
        }
        $upload_dir = __DIR__ . '/../uploads/site-images/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        $ext = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
        $max_size = 5 * 1024 * 1024;

        if (!in_array($ext, $allowed)) {
            api_json(['status' => 'error', 'message' => 'Invalid image type. Allowed: JPG, PNG, WebP, GIF, SVG.'], 422);
        }
        if ($_FILES['file']['size'] > $max_size) {
            api_json(['status' => 'error', 'message' => 'Image is too large. Max size is 5MB.'], 422);
        }

        $new_filename = 'site_' . time() . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
        if (!move_uploaded_file($_FILES['file']['tmp_name'], $upload_dir . $new_filename)) {
            api_json(['status' => 'error', 'message' => 'Failed to save upload.'], 500);
        }
        $url = 'uploads/site-images/' . $new_filename;
        upsert_override($conn, $image_key, $page_file, $original_src, $url);
        log_audit($conn, 'Update', 'SiteImage', 'Admin', $admin_id, "Uploaded image for $image_key ($url)");
        api_json(['status' => 'success', 'message' => 'Image uploaded.', 'url' => $url]);

    default:
        api_json(['status' => 'error', 'message' => 'Unknown action.'], 422);
}
