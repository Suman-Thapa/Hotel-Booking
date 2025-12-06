-- Create Database
CREATE DATABASE IF NOT EXISTS HotelBooking;
USE HotelBooking;

-- --------------------------------------------------------
-- USERS TABLE
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS users (
    user_id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) DEFAULT NULL,
    email VARCHAR(100) DEFAULT NULL,
    password VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    user_image VARCHAR(255) DEFAULT NULL,
    level ENUM('user','admin','hoteladmin') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- HOTELS TABLE
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS hotels (
    hotel_id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    hotel_name VARCHAR(150) DEFAULT NULL,
    location VARCHAR(150) DEFAULT NULL,
    created_by VARCHAR(50) DEFAULT NULL,
    hotel_image VARCHAR(255) DEFAULT NULL,
    about TEXT DEFAULT NULL,
    hotel_admin_id INT(11) DEFAULT NULL,
    FOREIGN KEY (hotel_admin_id) REFERENCES users(user_id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- HOTEL ROOM TABLE
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS hotel_room (
    room_id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    hotel_id INT(11) NOT NULL,
    room_number VARCHAR(50) NOT NULL,
    room_type ENUM('Single','Double','Suite') DEFAULT 'Single',
    price_per_room DECIMAL(10,2) NOT NULL,
    status ENUM('Available','Booked') DEFAULT 'Available',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    room_image VARCHAR(250) DEFAULT NULL,
    total_rooms INT(11) DEFAULT NULL,
    available_rooms INT(11) DEFAULT NULL,
    about_rooms TEXT DEFAULT NULL,
    FOREIGN KEY (hotel_id) REFERENCES hotels(hotel_id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- BOOKINGS TABLE
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS bookings (
    booking_id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    user_id INT(11) NOT NULL,
    hotel_id INT(11) NOT NULL,
    room_id INT(11) NOT NULL,
    rooms_booked INT(11) NOT NULL,
    check_in DATE NOT NULL,
    check_out DATE NOT NULL,
    status ENUM('booked','cancel_requested','canceled') DEFAULT 'booked',
    booked_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (hotel_id) REFERENCES hotels(hotel_id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (room_id) REFERENCES hotel_room(room_id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- PAYMENTS TABLE
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS payments (
    payment_id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    booking_id INT(11) DEFAULT NULL,
    user_id INT(11) DEFAULT NULL,
    amount DECIMAL(10,2) DEFAULT NULL,
    payment_method VARCHAR(50) DEFAULT NULL,
    transaction_id VARCHAR(100) DEFAULT NULL,
    payment_status ENUM('pending','completed','failed','paid') DEFAULT 'pending',
    payment_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (booking_id) REFERENCES bookings(booking_id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
