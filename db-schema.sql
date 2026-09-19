CREATE DATABASE IF NOT EXISTS driver_tp CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE driver_tp;

CREATE TABLE IF NOT EXISTS vehicles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    vehicle_type VARCHAR(50) NOT NULL,
    seats INT DEFAULT 4,
    status VARCHAR(20) DEFAULT 'active',
    base_price DECIMAL(12,2) DEFAULT 0.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS drivers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(120) NOT NULL,
    phone VARCHAR(30),
    status VARCHAR(20) DEFAULT 'online',
    verification_status VARCHAR(20) DEFAULT 'pending',
    rating DECIMAL(3,2) DEFAULT 0.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS routes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    start_point VARCHAR(120) NOT NULL,
    end_point VARCHAR(120) NOT NULL,
    duration_minutes INT DEFAULT 60,
    base_price DECIMAL(12,2) DEFAULT 0.00,
    status VARCHAR(20) DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(120),
    pickup_location VARCHAR(150) NOT NULL,
    destination_location VARCHAR(150) NOT NULL,
    travel_date DATETIME,
    passenger_count INT DEFAULT 1,
    vehicle_type VARCHAR(50) DEFAULT 'sedan',
    status VARCHAR(30) DEFAULT 'pending',
    total_amount DECIMAL(12,2) DEFAULT 0.00,
    payment_method VARCHAR(30) DEFAULT 'cash',
    payment_status VARCHAR(30) DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS pricing_rules (
    id INT AUTO_INCREMENT PRIMARY KEY,
    service_type VARCHAR(50) NOT NULL,
    vehicle_type VARCHAR(50) NOT NULL,
    base_fee DECIMAL(12,2) DEFAULT 0.00,
    per_km_rate DECIMAL(12,2) DEFAULT 0.00,
    per_minute_rate DECIMAL(12,2) DEFAULT 0.00,
    waiting_fee_per_minute DECIMAL(12,2) DEFAULT 0.00,
    night_surcharge DECIMAL(12,2) DEFAULT 0.00,
    airport_surcharge DECIMAL(12,2) DEFAULT 0.00,
    holiday_surcharge DECIMAL(12,2) DEFAULT 0.00,
    min_fare DECIMAL(12,2) DEFAULT 0.00,
    active_from TIMESTAMP NULL,
    active_to TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS trip_payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT NOT NULL,
    amount DECIMAL(12,2) DEFAULT 0.00,
    payment_method VARCHAR(30) NOT NULL,
    payment_status VARCHAR(30) DEFAULT 'pending',
    transaction_reference VARCHAR(120),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (booking_id) REFERENCES bookings(id)
);

CREATE TABLE IF NOT EXISTS driver_verifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    driver_id INT NOT NULL,
    document_type VARCHAR(50) NOT NULL,
    document_url VARCHAR(255),
    verification_status VARCHAR(20) DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (driver_id) REFERENCES drivers(id)
);

CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(60) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(120),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO admins (username, password_hash, full_name)
VALUES ('admin', '$2y$10$Rzo29yB1z8Spm8j1M1vB0uYcEid2jmXQeP0ocw2q2D9W9yQ4oB0fy', 'System Admin')
ON DUPLICATE KEY UPDATE full_name = VALUES(full_name);
