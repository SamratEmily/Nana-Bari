-- Create Database
CREATE DATABASE IF NOT EXISTS nanabari CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Use the Database
USE nanabari;

-- Create Gallery Table
CREATE TABLE IF NOT EXISTS gallery (
    id INT AUTO_INCREMENT PRIMARY KEY,
    image_data LONGTEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create Kobita (Poems) Table
CREATE TABLE IF NOT EXISTS kobita (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    body TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create Awards Table
CREATE TABLE IF NOT EXISTS awards (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create Deyalikha (Guestbook) Table
CREATE TABLE IF NOT EXISTS deyalikha (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert some sample data (optional)
-- You can remove these if you don't want sample data

-- Sample Gallery Image (placeholder)
-- INSERT INTO gallery (image_data) VALUES ('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAwIiBoZWlnaHQ9IjMwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iNDAwIiBoZWlnaHQ9IjMwMCIgZmlsbD0iI2NjYyIvPjx0ZXh0IHg9IjUwJSIgeT0iNTAlIiBmb250LWZhbWlseT0iQXJpYWwiIGZvbnQtc2l6ZT0iMjQiIGZpbGw9IiM2NjYiIHRleHQtYW5jaG9yPSJtaWRkbGUiIGR5PSIuM2VtIj5TYW1wbGUgSW1hZ2U8L3RleHQ+PC9zdmc+');

-- Sample Poem
-- INSERT INTO kobita (title, body) VALUES ('স্বাগতম', 'নানার বাড়িতে স্বাগতম\nএখানে রাখুন আপনার স্মৃতি\nছবি, কবিতা, সব কিছু');

-- Sample Award
-- INSERT INTO awards (name, description) VALUES ('বর্ষসেরা কবিতা', 'সেরা বাংলা কবিতার জন্য পুরস্কার');

-- Sample Guestbook Entry
-- INSERT INTO deyalikha (name, message) VALUES ('প্রশাসক', 'নানার বাড়িতে আপনাকে স্বাগতম!');

-- Display success message
SELECT 'Database and tables created successfully!' AS Status;
