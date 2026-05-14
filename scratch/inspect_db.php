<?php
include 'config.php';
$conn = get_db_connection();

$tables = ['projects', 'milestones', 'sub_milestones', 'admins', 'departments', 'milestone_assigned_tasks'];

foreach ($tables as $table) {
    echo "--- Table: $table ---\n";
    $res = $conn->query("DESCRIBE $table");
    if ($res) {
        while ($row = $res->fetch_assoc()) {
            echo "{$row['Field']} | {$row['Type']} | {$row['Null']} | {$row['Key']}\n";
        }
    } else {
        echo "Table not found.\n";
    }
    echo "\n";
}
