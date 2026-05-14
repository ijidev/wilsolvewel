<?php
include __DIR__ . '/../config.php';
$conn = get_db_connection();

$tables = ['milestones', 'sub_milestones', 'admins', 'departments'];

foreach ($tables as $table) {
    echo "=== $table ===\n";
    $res = @$conn->query("DESCRIBE `$table`");
    if ($res) {
        while ($row = $res->fetch_assoc()) {
            echo "  " . $row['Field'] . " | " . $row['Type'] . " | " . $row['Key'] . "\n";
        }
    } else {
        echo "  TABLE DOES NOT EXIST\n";
    }
    echo "\n";
}

// Also check for any task assignment tables
$extras = ['task_assignments', 'milestone_assignments', 'department_members', 'project_departments'];
echo "=== CHECKING EXTRA TABLES ===\n";
foreach ($extras as $table) {
    $res = @$conn->query("DESCRIBE `$table`");
    if ($res) {
        echo "  $table EXISTS:\n";
        while ($row = $res->fetch_assoc()) {
            echo "    " . $row['Field'] . " | " . $row['Type'] . "\n";
        }
    } else {
        echo "  $table - NOT FOUND\n";
    }
}
