<?php
/**
 * Migration: Add departments, leaders, task assignments
 */
include __DIR__ . '/../config.php';
$conn = get_db_connection();

$queries = [
    // Departments table (if not exists)
    "CREATE TABLE IF NOT EXISTS departments (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        leader_id INT NULL,
        description TEXT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (leader_id) REFERENCES admins(id) ON DELETE SET NULL
    )",

    // Department members
    "CREATE TABLE IF NOT EXISTS department_members (
        id INT AUTO_INCREMENT PRIMARY KEY,
        department_id INT NOT NULL,
        admin_id INT NOT NULL,
        role ENUM('Leader','Member') DEFAULT 'Member',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY (department_id, admin_id),
        FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE CASCADE,
        FOREIGN KEY (admin_id) REFERENCES admins(id) ON DELETE CASCADE
    )",

    // Add department_id to projects if not exists
    "ALTER TABLE projects ADD COLUMN IF NOT EXISTS department_id INT NULL",

    // Add assigned_to fields to sub_milestones (tasks)
    "ALTER TABLE project_sub_milestones ADD COLUMN IF NOT EXISTS assigned_to_admin INT NULL",
    "ALTER TABLE project_sub_milestones ADD COLUMN IF NOT EXISTS assigned_to_department INT NULL",

    // Add assigned_to fields to milestones
    "ALTER TABLE project_milestones ADD COLUMN IF NOT EXISTS assigned_to_department INT NULL",
];

echo "Running migration...\n";
foreach ($queries as $q) {
    if ($conn->query($q)) {
        echo "OK: " . substr($q, 0, 60) . "...\n";
    } else {
        echo "ERR: " . $conn->error . " | " . substr($q, 0, 60) . "\n";
    }
}
echo "\nMigration complete.\n";
