-- Project Milestones System
CREATE TABLE IF NOT EXISTS project_milestones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    order_index INT DEFAULT 0,
    status ENUM('Pending', 'In Progress', 'Completed') DEFAULT 'Pending',
    approval_status ENUM('Pending', 'Approved', 'Rejected') DEFAULT 'Pending',
    due_date DATE DEFAULT NULL,
    completed_at TIMESTAMP NULL DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE
);

-- Sub-Milestones (Optional)
CREATE TABLE IF NOT EXISTS project_sub_milestones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    milestone_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    is_completed TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (milestone_id) REFERENCES project_milestones(id) ON DELETE CASCADE
);

-- Link Project Reports to Milestones (Milestone Chat)
ALTER TABLE project_reports ADD COLUMN milestone_id INT DEFAULT NULL;
ALTER TABLE project_reports ADD CONSTRAINT fk_report_milestone FOREIGN KEY (milestone_id) REFERENCES project_milestones(id) ON DELETE CASCADE;
