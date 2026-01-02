-- ============================================
-- Hospital Leave Management System Database
-- ============================================
-- This file is OPTIONAL - the system auto-creates the database
-- Only use this if you want to manually set up the database
-- ============================================

-- Create Database
CREATE DATABASE IF NOT EXISTS hospital_leave_system CHARACTER SET utf8 COLLATE utf8_general_ci;
USE hospital_leave_system;

-- ============================================
-- Create Employees Table
-- ============================================
CREATE TABLE IF NOT EXISTS employees (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ============================================
-- Create Leaves Table
-- ============================================
CREATE TABLE IF NOT EXISTS leaves (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ============================================
-- Insert Sample Data (Optional - for testing)
-- ============================================

-- Sample Employees
INSERT INTO employees (full_name, nrc_number, email, phone, department, position, date_of_joining) VALUES
('Dr. Sarah Johnson', '123456/78/1', 'sarah.j@hospital.zm', '+260971234567', 'Emergency', 'Senior Doctor', '2020-03-15'),
('Nurse Mary Banda', '234567/89/2', 'mary.b@hospital.zm', '+260972345678', 'Nursing', 'Head Nurse', '2019-06-20'),
('Dr. John Mwale', '345678/90/3', 'john.m@hospital.zm', '+260973456789', 'Surgery', 'Surgeon', '2018-01-10'),
('Tech. Alice Phiri', '456789/01/4', 'alice.p@hospital.zm', '+260974567890', 'Laboratory', 'Lab Technician', '2021-09-05'),
('Admin Peter Lungu', '567890/12/5', 'peter.l@hospital.zm', '+260975678901', 'Administration', 'HR Manager', '2017-02-20');

-- Sample Leave Requests
INSERT INTO leaves (employee_id, leave_type, number_of_days, start_date, end_date, reason, status, request_date, approved_date) VALUES
(1, 'Annual Leave', 7, '2025-12-20', '2025-12-27', 'Family vacation during holidays', 'Approved', '2025-12-01', '2025-12-03'),
(2, 'Sick Leave', 3, '2025-12-15', '2025-12-18', 'Medical checkup and recovery', 'Pending', '2025-12-10', NULL),
(3, 'Emergency Leave', 2, '2025-12-18', '2025-12-20', 'Family emergency', 'Pending', '2025-12-16', NULL),
(1, 'Sick Leave', 5, '2025-11-05', '2025-11-10', 'Flu recovery', 'Approved', '2025-11-01', '2025-11-02'),
(4, 'Annual Leave', 10, '2025-12-23', '2026-01-02', 'Christmas and New Year vacation', 'Approved', '2025-11-15', '2025-11-18');

-- ============================================
-- Verification Queries (Optional - for testing)
-- ============================================

-- View all employees
-- SELECT * FROM employees;

-- View all leave requests
-- SELECT l.*, e.full_name as employee_name FROM leaves l JOIN employees e ON l.employee_id = e.id;

-- View pending leaves
-- SELECT l.*, e.full_name as employee_name FROM leaves l JOIN employees e ON l.employee_id = e.id WHERE l.status = 'Pending';

-- View active leaves (ongoing today)
-- SELECT l.*, e.full_name as employee_name FROM leaves l JOIN employees e ON l.employee_id = e.id 
-- WHERE l.status = 'Approved' AND l.start_date <= CURDATE() AND l.end_date >= CURDATE();