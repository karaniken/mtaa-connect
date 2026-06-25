-- Create the database
CREATE DATABASE IF NOT EXISTS mtaa_db;
USE mtaa_db;

-- Table: users (landlords and tenants)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fullname VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(20) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    user_type ENUM('landlord', 'tenant') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table: properties (listed by landlords)
CREATE TABLE properties (
    id INT AUTO_INCREMENT PRIMARY KEY,
    landlord_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    location VARCHAR(150) NOT NULL,
    bedrooms INT DEFAULT 0,
    bathrooms INT DEFAULT 0,
    size_sqft INT DEFAULT 0,
    status ENUM('vacant', 'occupied', 'booked') DEFAULT 'vacant',
    image_url VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (landlord_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Table: inquiries (tenants contacting landlords)
CREATE TABLE inquiries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    property_id INT NOT NULL,
    tenant_id INT NOT NULL,
    message TEXT NOT NULL,
    replied BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE,
    FOREIGN KEY (tenant_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Insert sample data (for testing)
INSERT INTO users (fullname, email, phone, password_hash, user_type) VALUES
('John Landlord', 'john@landlord.com', '0712345678', '$2y$10$dummyhash', 'landlord'),
('Mary Tenant', 'mary@tenant.com', '0723456789', '$2y$10$dummyhash', 'tenant');

INSERT INTO properties (landlord_id, title, description, price, location, bedrooms, bathrooms, size_sqft) VALUES
(1, 'Spacious 3-Bedroom Apartment', 'Fully furnished with parking and 24/7 security.', 25000.00, 'Nairobi, Kilimani', 3, 2, 1200);
