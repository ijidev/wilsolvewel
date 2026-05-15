<?php
include 'config.php';
$conn = get_db_connection();

// Check if project_id exists
$res = $conn->query("SHOW COLUMNS FROM hsse_observations LIKE 'project_id'");
if ($res->num_rows === 0) {
    $conn->query("ALTER TABLE hsse_observations ADD COLUMN project_id INT(11) NULL AFTER inspector_id");
    echo "Added project_id column.\n";
} else {
    echo "project_id column already exists.\n";
}

// Create audits table if not exists
$conn->query("CREATE TABLE IF NOT EXISTS hsse_audits (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    type VARCHAR(100) NOT NULL,
    location VARCHAR(255),
    audit_date DATE NOT NULL,
    status ENUM('Upcoming', 'Completed', 'Cancelled') DEFAULT 'Upcoming',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

echo "HSSE Tables synchronized.\n";
?>
