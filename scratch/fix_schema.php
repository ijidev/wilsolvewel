<?php
include 'config.php';
$conn = get_db_connection();
$sql = "ALTER TABLE departments ADD COLUMN leader_id INT NULL";
if ($conn->query($sql)) {
    echo "Added leader_id to departments\n";
} else {
    echo "Error adding leader_id: " . $conn->error . "\n";
}

$sql2 = "ALTER TABLE departments ADD CONSTRAINT fk_leader FOREIGN KEY (leader_id) REFERENCES admins(id) ON DELETE SET NULL";
if ($conn->query($sql2)) {
    echo "Added foreign key\n";
} else {
    echo "Error adding foreign key: " . $conn->error . "\n";
}
