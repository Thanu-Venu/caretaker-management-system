CREATE TABLE IF NOT EXISTS caretaker_profile_change_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    caretaker_id INT NOT NULL,
    requested_name VARCHAR(120) NOT NULL,
    requested_email VARCHAR(150) NOT NULL,
    requested_phone VARCHAR(30) NOT NULL,
    requested_experience VARCHAR(120) DEFAULT '',
    requested_location VARCHAR(120) DEFAULT '',
    requested_qualifications TEXT,
    status ENUM('Pending','Approved','Rejected') NOT NULL DEFAULT 'Pending',
    admin_note TEXT NULL,
    reviewed_by INT NULL,
    reviewed_at DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_profile_change_caretaker (caretaker_id),
    INDEX idx_profile_change_status (status)
);
