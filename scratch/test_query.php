<?php
include 'config.php';
$conn = get_db_connection();
$res = $conn->query("SELECT d.id, d.name, a.name as leader_name FROM departments d LEFT JOIN admins a ON d.leader_id = a.id ORDER BY d.name ASC");
if (!$res) {
    echo "ERROR: " . $conn->error;
} else {
    echo "SUCCESS";
}
