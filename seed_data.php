<?php
/**
 * Seed Data for Wilsolvewel - Management Team & Job Openings
 * Run this after setup_db.php to populate initial data.
 */

require_once __DIR__ . '/config.php';
$conn = get_db_connection();

echo "Seeding management team...\n";

$team_members = [
    [
        'name' => 'Engr. Adewale Ogunlesi',
        'position' => 'Managing Director',
        'bio' => 'Engineering & Operations Lead',
        'department' => 'Management',
        'sort_order' => 1,
        'status' => 'Active'
    ],
    [
        'name' => 'Chinedu Okonkwo',
        'position' => 'Procurement Director',
        'bio' => 'Supply Chain & Logistics',
        'department' => 'Procurement',
        'sort_order' => 2,
        'status' => 'Active'
    ],
    [
        'name' => 'Engr. Folake Adeyemi',
        'position' => 'Technical Director',
        'bio' => 'Maintenance & Field Operations',
        'department' => 'Technical',
        'sort_order' => 3,
        'status' => 'Active'
    ],
    [
        'name' => 'Ibrahim Bello',
        'position' => 'Finance Director',
        'bio' => 'Business Development & Strategy',
        'department' => 'Finance',
        'sort_order' => 4,
        'status' => 'Active'
    ]
];

$stmt = $conn->prepare("INSERT IGNORE INTO team_members (name, position, bio, department, sort_order, status) VALUES (?, ?, ?, ?, ?, ?)");
foreach ($team_members as $m) {
    $stmt->bind_param("ssssis", $m['name'], $m['position'], $m['bio'], $m['department'], $m['sort_order'], $m['status']);
    $stmt->execute();
    echo "  + {$m['name']} ({$m['position']})\n";
}
$stmt->close();

echo "Seeding job openings...\n";

$job_openings = [
    [
        'title' => 'Senior Mechanical Engineer',
        'department' => 'Industrial Design',
        'location' => 'Lagos HQ',
        'type' => 'Full-Time',
        'description' => 'Lead mechanical design and execution for industrial projects across oil & gas, power generation, and manufacturing sectors.',
        'sort_order' => 1,
        'status' => 'Open'
    ],
    [
        'title' => 'Procurement Specialist',
        'department' => 'Supply Chain',
        'location' => 'Port Harcourt',
        'type' => 'Full-Time',
        'description' => 'Manage end-to-end procurement of OEM parts and equipment, supplier qualification, and logistics coordination.',
        'sort_order' => 2,
        'status' => 'Open'
    ],
    [
        'title' => 'Technical Support Technician',
        'department' => 'Client Success',
        'location' => 'Abuja / Remote',
        'type' => 'Hybrid',
        'description' => 'Provide fast-response field support and remote diagnostics for industrial equipment across client sites.',
        'sort_order' => 3,
        'status' => 'Open'
    ]
];

$stmt = $conn->prepare("INSERT IGNORE INTO job_openings (title, department, location, type, description, sort_order, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
foreach ($job_openings as $j) {
    $stmt->bind_param("sssssis", $j['title'], $j['department'], $j['location'], $j['type'], $j['description'], $j['sort_order'], $j['status']);
    $stmt->execute();
    echo "  + {$j['title']} ({$j['location']})\n";
}
$stmt->close();

echo "\nSeed data complete!\n";
$conn->close();
