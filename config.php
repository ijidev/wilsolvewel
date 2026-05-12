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
 * Helper to ensure a column exists in a table (Self-Healing Migrations)
 */
function ensure_column_exists($conn, $table, $column, $definition) {
    $result = $conn->query("SHOW COLUMNS FROM $table LIKE '$column'");
    if ($result && $result->num_rows == 0) {
        $conn->query("ALTER TABLE $table ADD COLUMN $column $definition");
    }
}

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
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    $conn->query("CREATE TABLE IF NOT EXISTS admins (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255),
        email VARCHAR(255) UNIQUE NOT NULL,
        password VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // Self-Healing Column Checks
    ensure_column_exists($conn, 'departments', 'privilege_template_id', 'INT(11) NULL AFTER name');
    ensure_column_exists($conn, 'departments', 'permissions', 'JSON NULL AFTER privilege_template_id');
    ensure_column_exists($conn, 'admins', 'department_id', 'INT(11) NULL AFTER password');

    // Seed default departments
    $dept_check = $conn->query("SELECT COUNT(*) as total FROM departments");
    if ($dept_check && $dept_check->fetch_assoc()['total'] == 0) {
        $depts = ['Operations', 'Technical Support', 'Field Logistics', 'Safety & Compliance', 'Management'];
        foreach ($depts as $d) {
            $d = $conn->real_escape_string($d);
            $conn->query("INSERT IGNORE INTO departments (name) VALUES ('$d')");
        }
    }

    // Seed default admin
    $admin_check = $conn->query("SELECT COUNT(*) as total FROM admins");
    if ($admin_check && $admin_check->fetch_assoc()['total'] == 0) {
        $conn->query("INSERT INTO admins (name, email, password) VALUES ('Main Admin', 'admin@wilsolvewel.com', '" . password_hash('admin123', PASSWORD_DEFAULT) . "')");
    }

    // Seed default settings
    $result = $conn->query("SELECT COUNT(*) as total FROM settings");
    if ($result && $result->fetch_assoc()['total'] == 0) {
        $default_settings = [
            'smtp_host' => 'smtp.example.com',
            'smtp_port' => '587',
            'smtp_user' => 'user@example.com',
            'smtp_pass' => '',
            'smtp_encryption' => 'tls',
            'smtp_from_email' => 'noreply@wilsolvewel.com',
            'smtp_from_name' => 'Wilsolvewel Engineering'
        ];
        foreach ($default_settings as $key => $value) {
            $key = $conn->real_escape_string($key);
            $value = $conn->real_escape_string($value);
            $conn->query("INSERT IGNORE INTO settings (setting_key, setting_value) VALUES ('$key', '$value')");
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
    $env_val = getenv(strtoupper($key));
    if ($env_val !== false) return $env_val;
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

/**
 * Verify SMTP Connection
 */
function test_smtp_connection() {
    $host = get_setting('smtp_host');
    $port = (int)get_setting('smtp_port', 587);
    $timeout = 5;

    if (empty($host)) return ['status' => false, 'message' => 'SMTP Host not configured.'];

    // Try to open a socket to the SMTP host
    $errno = 0;
    $errstr = '';
    $fp = @fsockopen($host, $port, $errno, $errstr, $timeout);

    if ($fp) {
        fclose($fp);
        return ['status' => true, 'message' => 'Connection to ' . $host . ' successful.'];
    } else {
        return ['status' => false, 'message' => 'Could not connect to SMTP server: ' . $errstr];
    }
}
?>
