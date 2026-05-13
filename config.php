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

/**
 * Function to ensure a column exists in a table, adding it if missing
 */
function ensure_column_exists($conn, $table, $column, $definition) {
    $result = $conn->query("SHOW COLUMNS FROM `$table` LIKE '$column'");
    if ($result && $result->num_rows == 0) {
        $conn->query("ALTER TABLE `$table` ADD COLUMN `$column` $definition");
    }
}

function log_audit($conn, $action_type, $module, $actor_type, $actor_id, $description, $details = []) {
    $details_json = empty($details) ? 'NULL' : "'" . $conn->real_escape_string(json_encode($details)) . "'";
    $action_type = $conn->real_escape_string($action_type);
    $module = $conn->real_escape_string($module);
    $actor_type = $conn->real_escape_string($actor_type);
    $actor_id = (int)$actor_id;
    $description = $conn->real_escape_string($description);
    
    $sql = "INSERT INTO audit_logs (action_type, module, actor_type, actor_id, description, details) 
            VALUES ('$action_type', '$module', '$actor_type', $actor_id, '$description', $details_json)";
    $conn->query($sql);
}

function log_error($conn, $module, $error_message, $context = null) {
    $mod_safe = $conn->real_escape_string($module);
    $msg_safe = $conn->real_escape_string($error_message);
    $ctx_safe = $context ? "'" . $conn->real_escape_string(json_encode($context)) . "'" : "NULL";
    
    $conn->query("INSERT INTO system_errors (module, error_message, context) VALUES ('$mod_safe', '$msg_safe', $ctx_safe)");
}

// Custom Error and Exception Handlers to log to system_errors
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    if (!(error_reporting() & $errno)) {
        return false;
    }
    $conn = get_db_connection();
    if ($conn && !$conn->connect_error) {
        log_error($conn, 'PHP Warning/Error', "[$errno] $errstr", ['file' => $errfile, 'line' => $errline]);
    }
    return false; // let normal error handler also run
});

set_exception_handler(function($exception) {
    $conn = get_db_connection();
    if ($conn && !$conn->connect_error) {
        log_error($conn, 'PHP Exception', $exception->getMessage(), [
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString()
        ]);
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
        privilege_template_id INT(11) NULL,
        permissions JSON NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

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
        requested_by INT(11),
        project_id INT(11) NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

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

    $conn->query("CREATE TABLE IF NOT EXISTS projects (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        client_id INT(11) NOT NULL,
        name VARCHAR(255) NOT NULL,
        description TEXT NULL,
        status VARCHAR(50) DEFAULT 'Planning',
        start_date DATE NULL,
        end_date DATE NULL,
        budget DECIMAL(15,2) DEFAULT 0.00,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    $conn->query("CREATE TABLE IF NOT EXISTS project_reports (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        project_id INT(11) NOT NULL,
        admin_id INT(11) NOT NULL,
        report_date DATE NOT NULL,
        content TEXT NOT NULL,
        client_comment TEXT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    $conn->query("CREATE TABLE IF NOT EXISTS assets (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        project_id INT(11) NULL,
        name VARCHAR(255) NOT NULL,
        type VARCHAR(100) NOT NULL,
        status VARCHAR(50) DEFAULT 'Active',
        location VARCHAR(255) NULL,
        value DECIMAL(15,2) DEFAULT 0.00,
        purchase_date DATE NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

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

    $conn->query("CREATE TABLE IF NOT EXISTS ticket_replies (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        ticket_id INT(11) NOT NULL,
        sender_type VARCHAR(20) NOT NULL,
        sender_id INT(11) NOT NULL,
        message TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    $conn->query("CREATE TABLE IF NOT EXISTS audit_logs (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        action_type VARCHAR(50) NOT NULL,
        module VARCHAR(50) NOT NULL,
        actor_type VARCHAR(20) NOT NULL,
        actor_id INT(11) NOT NULL,
        description TEXT NOT NULL,
        details JSON NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
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

    // Seed default admin
    $admin_check = $conn->query("SELECT COUNT(*) as total FROM admins");
    if ($admin_check) {
        $row = $admin_check->fetch_assoc();
        if ($row['total'] == 0) {
            $conn->query("INSERT INTO admins (name, email, password, role, status) VALUES ('Main Admin', 'admin@wilsolvewel.com', '" . password_hash('admin123', PASSWORD_DEFAULT) . "', 'Director', 'Active')");
        } else {
            $conn->query("UPDATE admins SET role = 'Director', status = 'Active' WHERE id = 1 AND (role IS NULL OR status IS NULL)");
        }
    }

    return $conn;
}

function get_setting($key, $default = '') {
    $conn = get_db_connection();
    $key = $conn->real_escape_string($key);
    $result = $conn->query("SELECT setting_value FROM settings WHERE setting_key = '$key'");
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['setting_value'];
    }
    return $default;
}

function set_setting($key, $value) {
    $conn = get_db_connection();
    $key = $conn->real_escape_string($key);
    $value = $conn->real_escape_string($value);
    $conn->query("INSERT INTO settings (setting_key, setting_value) VALUES ('$key', '$value') ON DUPLICATE KEY UPDATE setting_value = '$value'");
}

function get_admin_permissions($admin_id) {
    $conn = get_db_connection();
    $admin_id = (int)$admin_id;
    $sql = "SELECT a.*, d.permissions as dept_perms, t.permissions as template_perms 
            FROM admins a
            LEFT JOIN departments d ON a.department_id = d.id
            LEFT JOIN privilege_templates t ON d.privilege_template_id = t.id
            WHERE a.id = $admin_id";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        $admin = $result->fetch_assoc();
        $perms = $admin['template_perms'] ?: $admin['dept_perms'];
        if ($perms) return json_decode($perms, true);
    }
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
?>
