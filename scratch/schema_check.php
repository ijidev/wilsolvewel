<?php
include 'config.php';
$conn = get_db_connection();
$res = $conn->query("SHOW COLUMNS FROM departments");
while ($r = $res->fetch_assoc()) {
    echo $r['Field'] . "\n";
}
