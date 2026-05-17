<?php
/**
 * Login As Staff - allows Director/admin to impersonate a staff member session
 * POST: staff_id, action=login_as | action=revert
 */
require_once '../config.php';
secure_session_start();

// Must be logged in as real admin
if (empty($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$conn = get_db_connection();
$real_admin_id = $_SESSION['real_admin_id'] ?? $_SESSION['admin_id'];
$action = $_POST['action'] ?? $_GET['action'] ?? '';

// ── REVERT: go back to real admin session ────────────────────────────────────
if ($action === 'revert') {
    if (!empty($_SESSION['real_admin_id'])) {
        // Restore original admin
        $stmt = $conn->prepare("SELECT * FROM admins WHERE id = ?");
        $stmt->bind_param("i", $_SESSION['real_admin_id']);
        $stmt->execute();
        $orig = $stmt->get_result();
        $stmt->close();
        if ($orig && $orig->num_rows > 0) {
            $admin = $orig->fetch_assoc();
            $_SESSION['admin_id']     = $admin['id'];
            $_SESSION['admin_name']   = $admin['name'];
            $_SESSION['admin_email']  = $admin['email'];
            $_SESSION['admin_role']   = $admin['role'];
            $_SESSION['admin_avatar'] = $admin['avatar'];
            unset($_SESSION['real_admin_id'], $_SESSION['is_impersonating'], $_SESSION['impersonating_name']);
            log_audit($conn, 'Revert', 'Auth', 'Admin', $admin['id'], 'Reverted from staff impersonation');
        }
    }
    header('Location: staff.php');
    exit;
}

// ── LOGIN AS: impersonate a staff member ─────────────────────────────────────
if ($action === 'login_as' && !empty($_POST['staff_id'])) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        csrf_error_response();
    }

    $staff_id = (int)$_POST['staff_id'];

    // Only Directors can impersonate
    if (($_SESSION['admin_role'] ?? '') !== 'Director' && empty($_SESSION['real_admin_id'])) {
        header('Location: staff.php?error=no_clearance');
        exit;
    }

    $stmt = $conn->prepare("SELECT * FROM admins WHERE id = ? AND status = 'Active' LIMIT 1");
    $stmt->bind_param("i", $staff_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    if ($result && $result->num_rows > 0) {
        $staff = $result->fetch_assoc();

        // Store real admin if not already impersonating
        if (empty($_SESSION['real_admin_id'])) {
            $_SESSION['real_admin_id'] = $_SESSION['admin_id'];
        }

        // Switch session to staff member
        $_SESSION['admin_id']          = $staff['id'];
        $_SESSION['admin_name']        = $staff['name'];
        $_SESSION['admin_email']       = $staff['email'];
        $_SESSION['admin_role']        = $staff['role'];
        $_SESSION['admin_avatar']      = $staff['avatar'];
        $_SESSION['is_impersonating']  = true;
        $_SESSION['impersonating_name'] = $staff['name'];

        log_audit($conn, 'Impersonate', 'Auth', 'Admin', $real_admin_id, 'Admin logged in as staff: ' . $staff['name'] . ' (ID:' . $staff['id'] . ')');

        header('Location: index.php');
        exit;
    }
}

header('Location: staff.php');
exit;
