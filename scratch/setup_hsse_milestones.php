<?php
include 'config.php';
$conn = get_db_connection();

$conn->query("CREATE TABLE IF NOT EXISTS hsse_milestones (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    observation_id INT(11) NULL,
    safe_days INT(11) NOT NULL,
    reset_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    reason VARCHAR(255)
)");

echo "HSSE Milestones table ready.\n";
?>
