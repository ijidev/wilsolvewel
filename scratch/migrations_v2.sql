-- Support Ticket File Uploads
ALTER TABLE ticket_replies ADD COLUMN attachment VARCHAR(255) DEFAULT NULL;

-- Project Log/Chat Transformation
ALTER TABLE project_reports ADD COLUMN sender_type ENUM('Admin', 'Client') DEFAULT 'Admin';
ALTER TABLE project_reports ADD COLUMN sender_id INT DEFAULT NULL;
-- Note: existing reports will default to 'Admin'

-- Project Asset Mapping
CREATE TABLE IF NOT EXISTS project_assets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT NOT NULL,
    asset_id INT NOT NULL,
    assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
    FOREIGN KEY (asset_id) REFERENCES assets(id) ON DELETE CASCADE
);
