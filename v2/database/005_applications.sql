CREATE TABLE IF NOT EXISTS staff_applications (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 habbo_name VARCHAR(40) NOT NULL,
 role_requested ENUM('dj','event_host') NOT NULL,
 experience TEXT NOT NULL,
 availability VARCHAR(250) NOT NULL,
 motivation TEXT NOT NULL,
 status ENUM('pending','accepted','declined') NOT NULL DEFAULT 'pending',
 reviewer_id BIGINT UNSIGNED NULL,
 reviewed_at DATETIME NULL,
 submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 INDEX idx_staff_app_status (status,role_requested,submitted_at),
 FOREIGN KEY (reviewer_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;