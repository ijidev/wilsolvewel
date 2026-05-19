<?php
/**
 * Simple .env loader to handle environment variables
 */
function loadEnv($path) {
    if (!file_exists($path)) {
        return;
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);
        if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
            putenv(sprintf('%s=%s', $name, $value));
            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
        }
    }
}

// Load .env file from root
loadEnv(__DIR__ . '/.env');

// Wilsovlewel Engineering - Database Configuration
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');
define('DB_NAME', getenv('DB_NAME') ?: 'wilsolvewel_db');

// ── Environment Configuration ───────────────────────────────────────────────
define('APP_ENV', getenv('APP_ENV') ?: 'production');
$script_dir = str_replace('\\', '/', dirname(__FILE__));
$doc_root = str_replace('\\', '/', realpath($_SERVER['DOCUMENT_ROOT']));
if (strpos($script_dir, $doc_root) === 0) {
    $relative = substr($script_dir, strlen($doc_root));
    define('APP_ROOT', rtrim($relative, '/'));
} else {
    define('APP_ROOT', rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? '/'), '/'));
}

function is_dev() {
    return APP_ENV === 'dev' || APP_ENV === 'development';
}

function app_url($path = '') {
    $path = ltrim($path, '/');
    return APP_ROOT . ($path ? '/' . $path : '');
}

if (is_dev()) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
}

// ── Session Security Configuration ──────────────────────────────────────────
function secure_session_start() {
    if (session_status() === PHP_SESSION_NONE) {
        $cookieParams = session_get_cookie_params();
        session_set_cookie_params([
            'lifetime' => $cookieParams['lifetime'],
            'path'     => $cookieParams['path'],
            'domain'   => $cookieParams['domain'],
            'secure'   => isset($_SERVER['HTTPS']),
            'httponly' => true,
            'samesite' => 'Strict'
        ]);
        session_start();
    }
}

// ── CSRF Protection ─────────────────────────────────────────────────────────
function generate_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function get_csrf_field() {
    return '<input type="hidden" name="csrf_token" value="' . generate_csrf_token() . '">';
}

function verify_csrf_token($token) {
    if (empty($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

function csrf_error_response($die = true) {
    if ($die) {
        die('Invalid or expired CSRF token. Please reload the page and try again.');
    }
    return false;
}

// ── Safe Query Helper (Prepared Statements) ─────────────────────────────────
function safe_query($conn, $sql, $types = '', $params = []) {
    if (empty($types)) {
        return $conn->query($sql);
    }
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        error_log("safe_query prepare failed: " . $conn->error . " | SQL: " . $sql);
        return false;
    }
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result;
}

// ── Schema Helpers ──────────────────────────────────────────────────────────
function ensure_column_exists($conn, $table, $column, $definition) {
    $result = $conn->query("SHOW COLUMNS FROM `$table` LIKE '$column'");
    if ($result && $result->num_rows == 0) {
        $conn->query("ALTER TABLE `$table` ADD COLUMN `$column` $definition");
    }
}

function log_audit($conn, $action_type, $module, $actor_type, $actor_id, $description, $details = []) {
    $details_json = empty($details) ? null : json_encode($details);
    $stmt = $conn->prepare("INSERT INTO audit_logs (action_type, module, actor_type, actor_id, description, details) VALUES (?, ?, ?, ?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("sssiss", $action_type, $module, $actor_type, $actor_id, $description, $details_json);
        $stmt->execute();
        $stmt->close();
    }
}

function log_error($conn, $module, $error_message, $context = null) {
    $context_json = $context ? json_encode($context) : null;
    $stmt = $conn->prepare("INSERT INTO system_errors (module, error_message, context) VALUES (?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("sss", $module, $error_message, $context_json);
        $stmt->execute();
        $stmt->close();
    }
}

// Custom Error and Exception Handlers
// In dev mode, errors display on screen. In production, they're silently logged.
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    if (!(error_reporting() & $errno)) {
        return false;
    }
    if (!is_dev()) {
        $conn = @get_db_connection();
        if ($conn && !$conn->connect_error) {
            @log_error($conn, 'PHP Warning/Error', "[$errno] $errstr", ['file' => $errfile, 'line' => $errline]);
        }
    }
    return false;
});

set_exception_handler(function($exception) {
    if (is_dev()) {
        echo '<div style="background:#FEE2E2;border:2px solid #EF4444;color:#991B1B;padding:20px;margin:20px;border-radius:8px;font-family:monospace;font-size:14px;line-height:1.6;">';
        echo '<strong style="font-size:16px;color:#DC2626;">🚨 Uncaught Exception</strong><br><br>';
        echo '<strong>Message:</strong> ' . htmlspecialchars($exception->getMessage()) . '<br>';
        echo '<strong>File:</strong> ' . htmlspecialchars($exception->getFile()) . ':' . $exception->getLine() . '<br>';
        echo '<strong>Trace:</strong><br><pre style="margin-top:8px;background:#FEF2F2;padding:12px;border-radius:4px;overflow-x:auto;">' . htmlspecialchars($exception->getTraceAsString()) . '</pre>';
        echo '</div>';
    } else {
        $conn = @get_db_connection();
        if ($conn && !$conn->connect_error) {
            @log_error($conn, 'PHP Exception', $exception->getMessage(), [
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $exception->getTraceAsString()
            ]);
        }
        if (ob_get_level()) ob_clean();
        http_response_code(500);
        include __DIR__ . '/errors/500.html';
    }
});

function get_db_connection() {
    $conn = @new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($conn->connect_error) {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        $conn->query("CREATE DATABASE IF NOT EXISTS " . DB_NAME);
        $conn->select_db(DB_NAME);
    }
    
    // Core Infrastructure Tables
    $conn->query("CREATE TABLE IF NOT EXISTS settings (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        setting_key VARCHAR(100) UNIQUE NOT NULL,
        setting_value TEXT,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )");
    
    $conn->query("CREATE TABLE IF NOT EXISTS inquiries (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255),
        email VARCHAR(255),
        type VARCHAR(50),
        subject VARCHAR(255),
        message TEXT,
        technical_data JSON,
        status VARCHAR(50) DEFAULT 'New',
        assigned_to VARCHAR(100) DEFAULT 'Unassigned',
        viewed_by JSON,
        forwarded_to VARCHAR(255),
        department_id INT(11) NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    $conn->query("CREATE TABLE IF NOT EXISTS privilege_templates (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) UNIQUE NOT NULL,
        permissions JSON,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    $conn->query("CREATE TABLE IF NOT EXISTS departments (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) UNIQUE NOT NULL,
        leader_id INT(11) NULL,
        privilege_template_id INT(11) NULL,
        permissions JSON NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    ensure_column_exists($conn, 'departments', 'leader_id', "INT(11) NULL");

    $conn->query("CREATE TABLE IF NOT EXISTS admins (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255),
        email VARCHAR(255) UNIQUE NOT NULL,
        password VARCHAR(255),
        department_id INT(11) NULL,
        role VARCHAR(100) DEFAULT 'Staff',
        status VARCHAR(50) DEFAULT 'Active',
        avatar VARCHAR(255) NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    ensure_column_exists($conn, 'admins', 'role', "VARCHAR(100) DEFAULT 'Staff'");
    ensure_column_exists($conn, 'admins', 'status', "VARCHAR(50) DEFAULT 'Active'");
    ensure_column_exists($conn, 'admins', 'avatar', "VARCHAR(255) NULL");
    ensure_column_exists($conn, 'admins', 'department_id', "INT(11) NULL");

    $conn->query("CREATE TABLE IF NOT EXISTS clients (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255),
        email VARCHAR(255) UNIQUE NOT NULL,
        phone VARCHAR(50),
        company VARCHAR(255),
        password VARCHAR(255) NULL,
        status VARCHAR(50) DEFAULT 'Active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    ensure_column_exists($conn, 'clients', 'password', "VARCHAR(255) NULL");

    ensure_column_exists($conn, 'clients', 'address', 'VARCHAR(255) NULL');
    ensure_column_exists($conn, 'clients', 'profile_pic', 'VARCHAR(255) NULL');

    $conn->query("CREATE TABLE IF NOT EXISTS client_password_tokens (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        client_id INT(11) NOT NULL,
        token VARCHAR(128) UNIQUE NOT NULL,
        expires_at DATETIME NOT NULL,
        used TINYINT(1) DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    $conn->query("CREATE TABLE IF NOT EXISTS procurement_orders (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        order_number VARCHAR(50) UNIQUE NOT NULL,
        tracking_id VARCHAR(100) NULL,
        item_name VARCHAR(255),
        quantity INT(11),
        unit_price DECIMAL(10,2),
        total_price DECIMAL(10,2),
        supplier VARCHAR(255),
        status VARCHAR(50) DEFAULT 'Pending',
        current_location VARCHAR(255) NULL,
        client_id INT(11) NULL,
        requested_by INT(11),
        project_id INT(11) NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    ensure_column_exists($conn, 'procurement_orders', 'client_id', "INT(11) NULL");
    ensure_column_exists($conn, 'procurement_orders', 'tracking_id', "VARCHAR(100) NULL");
    ensure_column_exists($conn, 'procurement_orders', 'current_location', "VARCHAR(255) NULL");
    ensure_column_exists($conn, 'procurement_orders', 'project_id', "INT(11) NULL");
    ensure_column_exists($conn, 'procurement_orders', 'updated_at', "TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP");

    $conn->query("CREATE TABLE IF NOT EXISTS procurement_history (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        order_id INT(11) NOT NULL,
        admin_id INT(11) NOT NULL,
        status VARCHAR(50) NOT NULL,
        location VARCHAR(255) NULL,
        tracking_id VARCHAR(100) NULL,
        notes TEXT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (order_id) REFERENCES procurement_orders(id) ON DELETE CASCADE
    )");
    ensure_column_exists($conn, 'procurement_history', 'notes', "TEXT NULL");

    $conn->query("CREATE TABLE IF NOT EXISTS projects (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        client_id INT(11) NOT NULL,
        department_id INT(11) NULL,
        name VARCHAR(255) NOT NULL,
        description TEXT NULL,
        status VARCHAR(50) DEFAULT 'Planning',
        budget DECIMAL(15,2) DEFAULT 0.00,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    ensure_column_exists($conn, 'projects', 'department_id', "INT(11) NULL");
    ensure_column_exists($conn, 'projects', 'start_date', 'DATE NULL');
    ensure_column_exists($conn, 'projects', 'end_date', 'DATE NULL');

    $conn->query("CREATE TABLE IF NOT EXISTS assets (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        project_id INT(11) NULL,
        name VARCHAR(255) NOT NULL,
        type VARCHAR(100) NOT NULL,
        status VARCHAR(50) DEFAULT 'Active',
        location VARCHAR(255) NULL,
        value DECIMAL(15,2) DEFAULT 0.00,
        purchase_date DATE NULL,
        file_path VARCHAR(500) NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    ensure_column_exists($conn, 'assets', 'file_path', "VARCHAR(500) NULL");

    $conn->query("CREATE TABLE IF NOT EXISTS tickets (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        client_id INT(11) NOT NULL,
        department_id INT(11) NULL,
        assigned_admin_id INT(11) NULL,
        project_id INT(11) NULL,
        subject VARCHAR(255) NOT NULL,
        description TEXT NOT NULL,
        status VARCHAR(50) DEFAULT 'Open',
        priority VARCHAR(20) DEFAULT 'Normal',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    ensure_column_exists($conn, 'tickets', 'project_id', "INT(11) NULL");
    ensure_column_exists($conn, 'tickets', 'order_id', "INT(11) NULL");

    $conn->query("CREATE TABLE IF NOT EXISTS ticket_replies (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        ticket_id INT(11) NOT NULL,
        sender_type VARCHAR(20) NOT NULL,
        sender_id INT(11) NOT NULL,
        message TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    $conn->query("CREATE TABLE IF NOT EXISTS hsse_observations (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        type VARCHAR(100) DEFAULT 'Routine',
        severity VARCHAR(50) DEFAULT 'Low',
        location VARCHAR(255) NULL,
        description TEXT,
        inspector_id INT(11) NULL,
        status VARCHAR(50) DEFAULT 'Open',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    $conn->query("CREATE TABLE IF NOT EXISTS maintenance_logs (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        asset_id INT(11) NOT NULL,
        admin_id INT(11) NOT NULL,
        action_taken TEXT,
        status VARCHAR(50) DEFAULT 'Completed',
        logged_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (asset_id) REFERENCES assets(id) ON DELETE CASCADE
    )");

    $conn->query("CREATE TABLE IF NOT EXISTS system_errors (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        module VARCHAR(50) NOT NULL,
        error_message TEXT NOT NULL,
        context JSON NULL,
        resolved TINYINT(1) DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    $conn->query("CREATE TABLE IF NOT EXISTS global_settings (
        setting_key VARCHAR(50) PRIMARY KEY,
        setting_value TEXT NULL,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )");

    $conn->query("CREATE TABLE IF NOT EXISTS routing_rules (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        source_type VARCHAR(50) NOT NULL,
        match_keyword VARCHAR(100) NULL,
        department_id INT(11) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    $conn->query("CREATE TABLE IF NOT EXISTS showcase_projects (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        category VARCHAR(100) NULL,
        client_name VARCHAR(255) NULL,
        year VARCHAR(20) NULL,
        description TEXT NULL,
        content LONGTEXT NULL,
        image_url VARCHAR(500) NULL,
        sort_order INT(11) DEFAULT 0,
        status VARCHAR(20) DEFAULT 'Active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    ensure_column_exists($conn, 'showcase_projects', 'content', "LONGTEXT NULL");

    $conn->query("CREATE TABLE IF NOT EXISTS faq_categories (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        slug VARCHAR(100) UNIQUE NOT NULL,
        icon VARCHAR(50) DEFAULT 'help',
        sort_order INT(11) DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    $conn->query("CREATE TABLE IF NOT EXISTS faqs (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        category_id INT(11) NOT NULL,
        question TEXT NOT NULL,
        answer LONGTEXT NOT NULL,
        sort_order INT(11) DEFAULT 0,
        status VARCHAR(20) DEFAULT 'Active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (category_id) REFERENCES faq_categories(id) ON DELETE CASCADE
    )");
    ensure_column_exists($conn, 'faqs', 'sort_order', "INT(11) DEFAULT 0");

    // ── HSSE TABLES ──────────────────────────────────────────────────────────
    ensure_column_exists($conn, 'hsse_observations', 'project_id', "INT(11) NULL");

    $conn->query("CREATE TABLE IF NOT EXISTS hsse_audits (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        type VARCHAR(100) NOT NULL,
        location VARCHAR(255),
        audit_date DATE NOT NULL,
        status ENUM('Upcoming','Completed','Cancelled') DEFAULT 'Upcoming',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    $conn->query("CREATE TABLE IF NOT EXISTS hsse_milestones (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        observation_id INT(11) NULL,
        safe_days INT(11) NOT NULL,
        reset_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        reason VARCHAR(255)
    )");

    $conn->query("CREATE TABLE IF NOT EXISTS hsse_observation_replies (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        observation_id INT(11) NOT NULL,
        admin_id INT(11) NOT NULL,
        message TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (observation_id) REFERENCES hsse_observations(id) ON DELETE CASCADE
    )");

    // ── PROJECT MILESTONES ────────────────────────────────────────────────────
    $conn->query("CREATE TABLE IF NOT EXISTS project_reports (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        project_id INT(11) NOT NULL,
        admin_id INT(11) NULL,
        sender_type ENUM('Admin','Client') DEFAULT 'Admin',
        sender_id INT(11) NULL,
        milestone_id INT(11) NULL,
        content TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE
    )");
    ensure_column_exists($conn, 'project_reports', 'sender_type', "ENUM('Admin','Client') DEFAULT 'Admin'");
    ensure_column_exists($conn, 'project_reports', 'sender_id', "INT(11) NULL");
    ensure_column_exists($conn, 'project_reports', 'milestone_id', "INT(11) NULL");

    $conn->query("CREATE TABLE IF NOT EXISTS project_milestones (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        project_id INT(11) NOT NULL,
        title VARCHAR(255) NOT NULL,
        description TEXT,
        order_index INT DEFAULT 0,
        status ENUM('Pending','In Progress','Completed') DEFAULT 'Pending',
        approval_status ENUM('Pending','Approved','Rejected') DEFAULT 'Pending',
        due_date DATE DEFAULT NULL,
        completed_at TIMESTAMP NULL DEFAULT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE
    )");

    $conn->query("CREATE TABLE IF NOT EXISTS project_sub_milestones (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        milestone_id INT(11) NOT NULL,
        title VARCHAR(255) NOT NULL,
        is_completed TINYINT(1) DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (milestone_id) REFERENCES project_milestones(id) ON DELETE CASCADE
    )");

    $conn->query("CREATE TABLE IF NOT EXISTS project_assets (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        project_id INT(11) NOT NULL,
        asset_id INT(11) NOT NULL,
        assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
        FOREIGN KEY (asset_id) REFERENCES assets(id) ON DELETE CASCADE
    )");

    // ── TICKETS ───────────────────────────────────────────────────────────────
    ensure_column_exists($conn, 'ticket_replies', 'attachment', "VARCHAR(255) NULL");

    // ── AUDIT LOGS ────────────────────────────────────────────────────────────
    $conn->query("CREATE TABLE IF NOT EXISTS audit_logs (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        action_type VARCHAR(50) NOT NULL,
        module VARCHAR(100) NOT NULL,
        actor_type VARCHAR(50) DEFAULT 'Admin',
        actor_id INT(11) NULL,
        description TEXT,
        details TEXT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    ensure_column_exists($conn, 'audit_logs', 'details', "TEXT NULL");

    // ── NOTIFICATIONS ─────────────────────────────────────────────────────────
    $conn->query("CREATE TABLE IF NOT EXISTS notifications (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        recipient_type ENUM('admin','client') NOT NULL,
        recipient_id INT(11) NOT NULL,
        title VARCHAR(255) NOT NULL,
        message TEXT,
        link VARCHAR(500),
        icon VARCHAR(50) DEFAULT 'notifications',
        is_read TINYINT(1) DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_recipient (recipient_type, recipient_id, is_read)
    )");

    // ── ADMIN SESSIONS (for Login-As feature) ─────────────────────────────────
    $conn->query("CREATE TABLE IF NOT EXISTS admin_sessions (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        admin_id INT(11) NOT NULL,
        impersonating_staff_id INT(11) NULL,
        session_token VARCHAR(128) UNIQUE NOT NULL,
        expires_at DATETIME NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // Prompt for initial admin password via env or setup
    $admin_check = $conn->query("SELECT COUNT(*) as total FROM admins");
    if ($admin_check) {
        $row = $admin_check->fetch_assoc();
        if ($row['total'] == 0) {
            $setup_token = bin2hex(random_bytes(16));
            $expires = date('Y-m-d H:i:s', strtotime('+48 hours'));
            $_SESSION['setup_token'] = $setup_token;
            $conn->query("CREATE TABLE IF NOT EXISTS admin_setup_tokens (
                id INT AUTO_INCREMENT PRIMARY KEY,
                token VARCHAR(64) UNIQUE NOT NULL,
                expires_at DATETIME NOT NULL,
                used TINYINT(1) DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )");
            $stmt = $conn->prepare("INSERT INTO admin_setup_tokens (token, expires_at) VALUES (?, ?)");
            $stmt->bind_param("ss", $setup_token, $expires);
            $stmt->execute();
            $stmt->close();
            $setup_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . "://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']) . "/admin/setup.php?token=$setup_token";
            echo '<div style="background:#FEF9C3;border:1px solid #EAB308;padding:16px;margin:20px 0;border-radius:8px;font-family:sans-serif;font-size:14px;">';
            echo '<strong>First-time Setup Required:</strong> Visit <a href="' . htmlspecialchars($setup_url) . '" style="color:#1A1A1A;font-weight:bold;">' . htmlspecialchars($setup_url) . '</a> to create the initial admin password.';
            echo '<br><small>This link expires in 48 hours.</small></div>';
        } else {
            $conn->query("UPDATE admins SET role = 'Director', status = 'Active' WHERE id = 1 AND (role IS NULL OR status IS NULL)");
        }
    }

    return $conn;
}

function get_setting($key, $default = '') {
    $conn = get_db_connection();
    $stmt = $conn->prepare("SELECT setting_value FROM settings WHERE setting_key = ?");
    if (!$stmt) return $default;
    $stmt->bind_param("s", $key);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $stmt->close();
        return $row['setting_value'];
    }
    $stmt->close();
    return $default;
}

function set_setting($key, $value) {
    $conn = get_db_connection();
    $stmt = $conn->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = ?");
    if ($stmt) {
        $stmt->bind_param("sss", $key, $value, $value);
        $stmt->execute();
        $stmt->close();
    }
}

function create_notification($conn, $recipient_type, $recipient_id, $title, $message = '', $link = '', $icon = 'notifications') {
    $stmt = $conn->prepare("INSERT INTO notifications (recipient_type, recipient_id, title, message, link, icon) VALUES (?, ?, ?, ?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("sissss", $recipient_type, $recipient_id, $title, $message, $link, $icon);
        $stmt->execute();
        $stmt->close();
    }
}

/**
 * Intelligent Auto-Routing
 * Finds the best department based on source type and keyword matching
 */
function get_auto_assigned_department($conn, $source_type, $content = '') {
    // 1. Try keyword matching first (most specific)
    if (!empty($content)) {
        $stmt = $conn->prepare("SELECT department_id, match_keyword FROM routing_rules WHERE source_type = ? AND match_keyword != '' AND match_keyword IS NOT NULL");
        if ($stmt) {
            $stmt->bind_param("s", $source_type);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($rule = $result->fetch_assoc()) {
                if (stripos($content, $rule['match_keyword']) !== false) {
                    $stmt->close();
                    return $rule['department_id'];
                }
            }
            $stmt->close();
        }
    }

    // 2. Try general type matching (no keyword)
    $stmt = $conn->prepare("SELECT department_id FROM routing_rules WHERE source_type = ? AND (match_keyword = '' OR match_keyword IS NULL) LIMIT 1");
    if ($stmt) {
        $stmt->bind_param("s", $source_type);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $stmt->close();
            return $row['department_id'];
        }
        $stmt->close();
    }

    return null; // Unassigned
}

function get_admin_permissions($admin_id) {
    $conn = get_db_connection();
    $stmt = $conn->prepare("SELECT a.*, d.permissions as dept_perms, t.permissions as template_perms 
            FROM admins a
            LEFT JOIN departments d ON a.department_id = d.id
            LEFT JOIN privilege_templates t ON d.privilege_template_id = t.id
            WHERE a.id = ?");
    if (!$stmt) return null;
    $stmt->bind_param("i", $admin_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result && $result->num_rows > 0) {
        $admin = $result->fetch_assoc();
        $stmt->close();
        $perms = $admin['template_perms'] ?: $admin['dept_perms'];
        if ($perms) return json_decode($perms, true);
        return null;
    }
    $stmt->close();
    return null; 
}

function test_smtp_connection() {
    $host = get_setting('smtp_host');
    $port = (int)get_setting('smtp_port', 587);
    $timeout = 5;
    if (empty($host)) return ['status' => false, 'message' => 'SMTP Host not configured.'];
    $fp = @fsockopen($host, $port, $errno, $errstr, $timeout);
    if ($fp) {
        fclose($fp);
        return ['status' => true, 'message' => 'Connection successful.'];
    } else {
        return ['status' => false, 'message' => 'Connection failed: ' . $errstr];
    }
}

function email_template($title, $body_html) {
    $brand = htmlspecialchars(get_setting('smtp_from_name', 'WilsOveWel Engineering'));
    return '<!DOCTYPE html><html><head><meta charset="UTF-8"><style>body{margin:0;padding:0;background:#F1F5F9;font-family:Manrope,Helvetica,Arial,sans-serif}.wrapper{padding:32px 16px}.card{max-width:560px;margin:0 auto;background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,0.08)}.header{background:#0F172A;padding:24px 32px}.header h1{color:#EAB308;font-size:18px;font-weight:700;margin:0;letter-spacing:-0.3px}.header span{color:#64748B;font-size:11px}.body{padding:32px}.body h2{color:#0F172A;font-size:16px;font-weight:700;margin:0 0 16px}.body p{color:#334155;font-size:14px;line-height:1.7;margin:0 0 12px}.body a{color:#2563EB;text-decoration:underline}.footer{background:#F8FAFC;padding:16px 32px;text-align:center;border-top:1px solid #E2E8F0}.footer p{color:#94A3B8;font-size:11px;margin:0}</style></head><body><div class="wrapper"><div class="card"><div class="header"><h1>' . $brand . '</h1><span>Notification</span></div><div class="body"><h2>' . $title . '</h2>' . $body_html . '</div><div class="footer"><p>&copy; ' . date('Y') . ' ' . $brand . '. All rights reserved.</p></div></div></div></body></html>';
}

function get_department_admins($conn, $department_id = null) {
    $sql = "SELECT id, email, name FROM admins WHERE status = 'Active'";
    if ($department_id) $sql .= " AND department_id = " . (int)$department_id;
    $res = $conn->query($sql);
    $admins = [];
    if ($res) while ($a = $res->fetch_assoc()) $admins[] = $a;
    return $admins;
}

function notify_department_admins($conn, $department_id, $title, $message, $link, $icon, $email_subject, $email_html) {
    $admins = get_department_admins($conn, $department_id);
    if (empty($admins)) $admins = get_department_admins($conn, null);
    foreach ($admins as $a) {
        create_notification($conn, 'admin', $a['id'], $title, $message, $link, $icon);
        send_email($a['email'], $email_subject, $email_html);
    }
    return !empty($admins);
}

function send_email($to, $subject, $html_body, $text_body = '') {
    $host = get_setting('smtp_host');
    $port = (int)get_setting('smtp_port', 587);
    $user = get_setting('smtp_user');
    $pass = get_setting('smtp_pass');
    $enc = get_setting('smtp_encryption', 'tls');
    $from_email = get_setting('smtp_from_email', 'noreply@wilsolvewel.com');
    $from_name = get_setting('smtp_from_name', 'WilsOveWel Engineering');

    if (empty($from_email)) $from_email = 'noreply@wilsolvewel.com';
    if (empty($from_name)) $from_name = 'WilsOveWel Engineering';
    if (empty($text_body)) $text_body = strip_tags($html_body);

    if (empty($host)) return false;

    return _smtp_send($host, $port, $user, $pass, $enc, $from_email, $from_name, $to, $subject, $html_body, $text_body);
}

function _smtp_cmd($fp, $cmd) { fwrite($fp, $cmd . "\r\n"); $line = fgets($fp, 512); return $line; }
function _smtp_code($line) { return substr(trim($line), 0, 3); }
function _smtp_read_ehlo($fp) { $line = ''; do { $line = fgets($fp, 512); } while (substr(trim($line), 3, 1) === '-'); return $line; }

function _smtp_send($host, $port, $user, $pass, $enc, $from_email, $from_name, $to, $subject, $html_body, $text_body) {
    $errno = 0; $errstr = '';
    $fp = @fsockopen($host, $port, $errno, $errstr, 15);
    if (!$fp) return false;

    $line = fgets($fp, 512);
    if (_smtp_code($line) !== '220') { fclose($fp); return false; }

    $helo = 'EHLO WilsOveWel';
    $line = _smtp_cmd($fp, $helo);
    if (_smtp_code($line) !== '250') { fclose($fp); return false; }
    $line = _smtp_read_ehlo($fp);

    if ($enc === 'tls') {
        $line = _smtp_cmd($fp, 'STARTTLS');
        if (_smtp_code($line) === '220') {
            if (!@stream_socket_enable_crypto($fp, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) { fclose($fp); return false; }
            $line = _smtp_cmd($fp, $helo);
            if (_smtp_code($line) !== '250') { fclose($fp); return false; }
            $line = _smtp_read_ehlo($fp);
        }
    }

    if (!empty($user) && !empty($pass)) {
        $line = _smtp_cmd($fp, 'AUTH LOGIN');
        if (_smtp_code($line) === '334') {
            $line = _smtp_cmd($fp, base64_encode($user));
            if (_smtp_code($line) === '334') {
                $line = _smtp_cmd($fp, base64_encode($pass));
                if (_smtp_code($line) !== '235') { fclose($fp); return false; }
            } else { fclose($fp); return false; }
        }
    }

    $line = _smtp_cmd($fp, "MAIL FROM:<$from_email>");
    if (_smtp_code($line) !== '250') { fclose($fp); return false; }

    $to_clean = trim(preg_replace('/^.*<(.*)>.*$/', '$1', $to));
    $line = _smtp_cmd($fp, "RCPT TO:<$to_clean>");
    if (_smtp_code($line) !== '250' && _smtp_code($line) !== '251') { fclose($fp); return false; }

    $line = _smtp_cmd($fp, 'DATA');
    if (_smtp_code($line) !== '354') { fclose($fp); return false; }

    $headers = "From: $from_name <$from_email>\r\n";
    $headers .= "Reply-To: $from_email\r\n";
    $headers .= "To: $to\r\n";
    $headers .= "Subject: =?UTF-8?B?" . base64_encode($subject) . "?=\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: multipart/alternative; boundary=\"alt-boundary\"\r\n";
    $headers .= "X-Mailer: WilsOveWel/1.0\r\n\r\n";
    $headers .= "--alt-boundary\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n\r\n" . $text_body . "\r\n\r\n";
    $headers .= "--alt-boundary\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n\r\n" . $html_body . "\r\n\r\n";
    $headers .= "--alt-boundary--\r\n.\r\n";

    fwrite($fp, $headers);
    $line = fgets($fp, 512);

    fclose($fp);
    return _smtp_code($line) === '250';
}
?>
