-- Create database (optional)
-- CREATE DATABASE nfc_manager CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- USE nfc_manager;

CREATE TABLE IF NOT EXISTS admins (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) UNIQUE NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS tags (
  id INT AUTO_INCREMENT PRIMARY KEY,
  slug VARCHAR(20) UNIQUE NOT NULL,
  uid_hex VARCHAR(32),
  label VARCHAR(100),
  status ENUM('active','lost','inactive') DEFAULT 'active',
  target_type ENUM('url','profile','file','message') DEFAULT 'url',
  target_value TEXT,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS scans (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  tag_id INT NOT NULL,
  scanned_at DATETIME NOT NULL,
  ip VARCHAR(45),
  user_agent TEXT,
  lat DECIMAL(9,6),
  lng DECIMAL(9,6),
  accuracy_m INT,
  city VARCHAR(100),
  region VARCHAR(100),
  country VARCHAR(100),
  FOREIGN KEY (tag_id) REFERENCES tags(id) ON DELETE CASCADE
);

-- default admin: admin / admin123  (change after login!)
INSERT IGNORE INTO admins (username, password_hash) VALUES
  ('admin', '$2y$10$9JmVQeZ9i9zQFv3wY5qTQe1kQ2mVv5hKp5Wm3s8vXG1V0qU4mX0Si'); 
-- hash = password_hash('admin123', PASSWORD_DEFAULT) produced beforehand