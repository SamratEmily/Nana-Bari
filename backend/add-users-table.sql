-- Add Users Table
USE nanabari;

-- Create Users Table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    name VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert Admin User
-- Password is stored as plain text for now (shahnoormaymuna@gmail.com)
INSERT INTO users (email, password, name) VALUES 
('shahnoormaymuna@gmail.com', 'shahnoormaymuna@gmail.com', 'Administrator')
ON DUPLICATE KEY UPDATE password = 'shahnoormaymuna@gmail.com';

-- Verify
SELECT id, email, name, created_at FROM users;
