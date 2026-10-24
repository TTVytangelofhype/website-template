CREATE TABLE IF NOT EXISTS panel_access_denials (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 user_id BIGINT UNSIGNED NULL,
 ip_address VARCHAR(45) NOT NULL,
 user_agent VARCHAR(255) NOT NULL DEFAULT '',
 reason VARCHAR(40) NOT NULL,
 requested_path VARCHAR(160) NOT NULL DEFAULT '',
 occurred_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 INDEX idx_denial_date (occurred_at),
 INDEX idx_denial_user (user_id,occurred_at),
 FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;