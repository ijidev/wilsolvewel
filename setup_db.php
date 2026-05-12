<?php
include 'config.php';

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database
$sql = "CREATE DATABASE IF NOT EXISTS " . DB_NAME;
if ($conn->query($sql) === TRUE) {
    echo "Database created successfully\n";
} else {
    echo "Error creating database: " . $conn->error . "\n";
}

$conn->select_db(DB_NAME);

// Create settings table
$sql = "CREATE TABLE IF NOT EXISTS settings (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";
if ($conn->query($sql) === TRUE) {
    echo "Table 'settings' created successfully\n";
}

// Create inquiries table
$sql = "CREATE TABLE IF NOT EXISTS inquiries (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255),
    email VARCHAR(255),
    type VARCHAR(50), -- 'Contact', 'Pump', 'Sourcing'
    subject VARCHAR(255),
    message TEXT,
    technical_data JSON, -- Stores form-specific fields
    status VARCHAR(50) DEFAULT 'New', -- 'New', 'Processing', 'Closed'
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
if ($conn->query($sql) === TRUE) {
    echo "Table 'inquiries' created successfully\n";
}

// Insert default SMTP settings if not exists
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
    $conn->query("INSERT IGNORE INTO settings (setting_key, setting_value) VALUES ('$key', '$value')");
}

$conn->close();
echo "Database setup complete.";
?>
