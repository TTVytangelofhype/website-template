CREATE TABLE IF NOT EXISTS song_requests (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 requester VARCHAR(40) NOT NULL,
 song VARCHAR(180) NOT NULL,
 status ENUM('waiting','played','rejected') NOT NULL DEFAULT 'waiting',
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 INDEX idx_song_requests_status (status,created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;