USE mtaa_db;

-- Drop child tables first to avoid foreign key errors
DROP TABLE IF EXISTS inquiries;
DROP TABLE IF EXISTS units;
DROP TABLE IF EXISTS properties;

-- Create the new properties table (the building/complex)
CREATE TABLE properties (
    id INT AUTO_INCREMENT PRIMARY KEY,
    landlord_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    location VARCHAR(150) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (landlord_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Create the units table (individual apartments/rooms)
CREATE TABLE units (
    id INT AUTO_INCREMENT PRIMARY KEY,
    property_id INT NOT NULL,
    floor_number INT DEFAULT 0,
    house_number VARCHAR(50) NOT NULL,
    size VARCHAR(50) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    amenities TEXT,
    status ENUM('vacant', 'occupied', 'booked') DEFAULT 'vacant',
    image_url VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE
);

-- Insert sample property (adjust landlord_id to match your existing landlord user)
INSERT INTO properties (landlord_id, title, location, description) VALUES
(1, 'Kilimani Heights', 'Nairobi, Kilimani', 'Modern apartments with 24/7 security and parking.');

-- Insert sample units
INSERT INTO units (property_id, floor_number, house_number, size, price, amenities, status) VALUES
(1, 2, 'Apt 201', '2BR', 25000.00, 'WiFi, Parking, Gym', 'vacant'),
(1, 3, 'Apt 301', '1BR', 18000.00, 'WiFi, Parking', 'vacant'),
(1, 1, 'Apt 102', 'Studio', 12000.00, 'Parking', 'booked');
