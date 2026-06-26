USE mtaa_db;

-- Add new columns to properties
ALTER TABLE properties
ADD COLUMN property_type ENUM('residential','commercial','land','short_term') DEFAULT 'residential',
ADD COLUMN listing_type ENUM('sale','rent','lease','short_let') DEFAULT 'rent',
ADD COLUMN is_featured BOOLEAN DEFAULT FALSE;

-- Add commercial category to units (for commercial properties)
ALTER TABLE units
ADD COLUMN commercial_category VARCHAR(50) DEFAULT NULL; -- e.g., 'carwash', 'garage', 'office', 'shop', 'stall', 'warehouse'

-- Create unit_media table for images/videos
CREATE TABLE unit_media (
    id INT AUTO_INCREMENT PRIMARY KEY,
    unit_id INT NOT NULL,
    media_type ENUM('image','video') DEFAULT 'image',
    url VARCHAR(255) NOT NULL,
    label VARCHAR(100) NOT NULL, -- e.g., 'Living Room', 'Bedroom', 'Kitchen', 'Exterior'
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (unit_id) REFERENCES units(id) ON DELETE CASCADE
);

-- Insert sample media (optional, for testing)
-- INSERT INTO unit_media (unit_id, media_type, url, label) VALUES
-- (1, 'image', '/uploads/apt201_living.jpg', 'Living Room'),
-- (1, 'image', '/uploads/apt201_bedroom.jpg', 'Bedroom');
