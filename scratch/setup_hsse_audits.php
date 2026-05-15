<?php
include 'config.php';
$conn = get_db_connection();

// Create Table
$conn->query("CREATE TABLE IF NOT EXISTS hsse_audits (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    type VARCHAR(100),
    location VARCHAR(255),
    audit_date DATE,
    status VARCHAR(50) DEFAULT 'Upcoming',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// Seed Data
$check = $conn->query("SELECT COUNT(*) as total FROM hsse_audits");
$row = $check->fetch_assoc();
if ($row['total'] == 0) {
    $conn->query("INSERT INTO hsse_audits (title, type, location, audit_date) VALUES 
    ('ISO 14001 Review', 'Environmental', 'Terminal A', '2026-10-24'),
    ('Fire System Test', 'Safety', 'Zone 08 / Farm', '2026-10-28')");
}

echo "HSSE Audits table ready.\n";
?>
