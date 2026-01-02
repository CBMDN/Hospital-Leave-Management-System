<?php
/**
 * Database Configuration File
 * 
 * This file contains all database connection settings
 * Modify these constants based on your database setup
 */

// Database Configuration Constants
define('DB_HOST', 'localhost');        // Database host (usually 'localhost')
define('DB_USER', 'root');             // Database username (default: 'root')
define('DB_PASS', 'CH25800');                 // Database password (default: empty for XAMPP)
define('DB_NAME', 'hospital_leave_system'); // Database name

/**
 * Create database connection
 * 
 * @return mysqli Database connection object
 */
function getDBConnection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    // Set charset to utf8
    $conn->set_charset("utf8");
    
    return $conn;
}

/**
 * Initialize Database and Tables
 * 
 * This function creates the database and required tables if they don't exist
 * It runs automatically when this file is included
 */
function initializeDatabase() {
    // Connect without selecting database first
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    // Create database if it doesn't exist
    $sql = "CREATE DATABASE IF NOT EXISTS " . DB_NAME . " CHARACTER SET utf8 COLLATE utf8_general_ci";
    if (!$conn->query($sql)) {
        die("Error creating database: " . $conn->error);
    }
    
    // Select the database
    $conn->select_db(DB_NAME);
    
    // Create employees table
    $sql = "CREATE TABLE IF NOT EXISTS employees (
        id INT AUTO_INCREMENT PRIMARY KEY,
        full_name VARCHAR(255) NOT NULL,
        nrc_number VARCHAR(50) NOT NULL UNIQUE,
        email VARCHAR(255) NOT NULL,
        phone VARCHAR(20) NOT NULL,
        department VARCHAR(100) NOT NULL,
        position VARCHAR(100) NOT NULL,
        date_of_joining DATE NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_nrc (nrc_number),
        INDEX idx_name (full_name)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
    
    if (!$conn->query($sql)) {
        die("Error creating employees table: " . $conn->error);
    }
    
    // Create leaves table
    $sql = "CREATE TABLE IF NOT EXISTS leaves (
        id INT AUTO_INCREMENT PRIMARY KEY,
        employee_id INT NOT NULL,
        leave_type VARCHAR(100) NOT NULL,
        number_of_days INT NOT NULL,
        start_date DATE NOT NULL,
        end_date DATE NOT NULL,
        reason TEXT NOT NULL,
        status ENUM('Pending', 'Approved', 'Rejected') DEFAULT 'Pending',
        request_date DATE NOT NULL,
        approved_date DATE NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE,
        INDEX idx_employee (employee_id),
        INDEX idx_status (status),
        INDEX idx_dates (start_date, end_date)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
    
    if (!$conn->query($sql)) {
        die("Error creating leaves table: " . $conn->error);
    }
    
    $conn->close();
}

// Initialize database on first load
initializeDatabase();

// Uncomment the line below to see successful connection message (for debugging)
// echo "Database initialized successfully!";
?>