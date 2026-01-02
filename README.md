# 🏥 Hospital Leave Management System

A comprehensive web-based leave management system designed for Chilenje Hospital to efficiently manage employee information and leave requests.

## 📋 Table of Contents
- [About the Project](#about-the-project)
- [Features](#features)
- [Technologies Used](#technologies-used)
- [System Requirements](#system-requirements)
- [Installation Guide](#installation-guide)
- [Database Setup](#database-setup)
- [Usage Guide](#usage-guide)
- [File Structure](#file-structure)
- [Security Features](#security-features)
- [Troubleshooting](#troubleshooting)

## 🎯 About the Project

The Hospital Leave Management System is a fully-featured web application built to streamline the process of managing hospital staff leave requests. It provides an intuitive interface for administrators to add employees, process leave requests, and track leave statistics in real-time.

### Key Objectives
- Digitize the leave request and approval process
- Maintain centralized employee records
- Provide real-time leave statistics and tracking
- Ensure secure access through user authentication
- Simplify leave management workflow for hospital administrators

## ✨ Features

### 1. **User Authentication**
- Secure login system with username and password
- Session management for logged-in users
- Password visibility toggle for ease of use
- Support for both plain text and hashed passwords

### 2. **Dashboard**
- Real-time statistics display:
  - Total Employees
  - Pending Leave Requests
  - Approved Leaves
  - Active Leaves (currently on leave)
- Recent leave requests overview
- Quick navigation to all system sections

### 3. **Employee Management**
- Add new employees with complete details:
  - Full Name
  - NRC Number
  - Email Address
  - Phone Number
  - Department
  - Position
  - Date of Joining
- View all employees in a detailed table
- Edit employee information
- Delete employee records
- Search employees by name or NRC number

### 4. **Leave Request Management**
- Submit leave requests with:
  - Employee selection
  - Leave type (Annual, Sick, Maternity, Paternity, Emergency, Unpaid)
  - Start and end dates
  - Number of days (auto-calculated)
  - Reason for leave
- Automatic date calculations:
  - Enter start date + days → auto-fills end date
  - Enter start date + end date → auto-calculates days
- Track leave request status (Pending, Approved, Rejected, Completed)

### 5. **Leave Approval System**
- Review all leave requests
- Approve or reject pending requests
- Edit leave request details
- Delete leave requests
- Automatic status updates to "Completed" when leave period ends
- Visual status badges for easy identification

### 6. **Search Functionality**
- Search employees by name or NRC number
- View detailed search results in formatted tables

## 🛠️ Technologies Used

### Frontend
- **HTML5** - Structure and content
- **CSS3** - Styling with gradients and modern design
- **JavaScript** - Dynamic interactions and AJAX requests
- **Responsive Design** - Mobile-friendly interface

### Backend
- **PHP 7.4+** - Server-side logic and processing
- **MySQL** - Database management
- **Session Management** - User authentication

### Design
- Modern purple gradient theme (#667eea to #764ba2)
- Card-based layouts
- Smooth animations and hover effects
- Professional form styling

## 💻 System Requirements

- **Web Server**: Apache 2.4+ or Nginx
- **PHP**: Version 7.4 or higher
- **Database**: MySQL 5.7+ or MariaDB 10.3+
- **Browser**: Modern browser (Chrome, Firefox, Safari, Edge)
- **Operating System**: Windows, Linux, or macOS

## 📥 Installation Guide

### Step 1: Download/Clone the Project
```bash
# If using Git
git clone <repository-url>

# Or download and extract the ZIP file
```

### Step 2: Set Up Web Server
1. Place all project files in your web server's document root:
   - **XAMPP**: `C:\xampp\htdocs\hospital_leave_system\`
   - **WAMP**: `C:\wamp64\www\hospital_leave_system\`
   - **Linux**: `/var/www/html/hospital_leave_system/`

### Step 3: Configure Database Connection
1. Open `config.php` and update database credentials:
```php
$servername = "localhost";
$username = "root";        // Your MySQL username
$password = "YOUR_PASSWORD"; // Your MySQL password
$dbname = "hospital_leave_system";
```

2. Update the same credentials in:
   - `login.php`
   - `view_employees.php`

## 🗄️ Database Setup

### Step 1: Create Database
```sql
CREATE DATABASE hospital_leave_system;
USE hospital_leave_system;
```

### Step 2: Create Tables

#### Employees Table
```sql
CREATE TABLE employees (
    id INT PRIMARY KEY AUTO_INCREMENT,
    full_name VARCHAR(255) NOT NULL,
    nrc_number VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    department VARCHAR(100) NOT NULL,
    position VARCHAR(100) NOT NULL,
    date_of_joining DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

#### Leaves Table
```sql
CREATE TABLE leaves (
    id INT PRIMARY KEY AUTO_INCREMENT,
    employee_id INT NOT NULL,
    leave_type VARCHAR(50) NOT NULL,
    number_of_days INT NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    reason TEXT NOT NULL,
    status ENUM('Pending', 'Approved', 'Rejected', 'Completed') DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE
);
```

#### Users Table (for login)
```sql
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### Step 3: Create Admin User

**Option 1: Plain Text Password (for testing)**
```sql
INSERT INTO users (username, password) 
VALUES ('admin', 'admin123');
```

**Option 2: Hashed Password (recommended for production)**
```sql
INSERT INTO users (username, password) 
VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');
-- Password: admin123
```

To create a hashed password, create a PHP file with:
```php
<?php
echo password_hash("your_password", PASSWORD_DEFAULT);
?>
```

### Step 4: Sample Data (Optional)

```sql
-- Sample Employees
INSERT INTO employees (full_name, nrc_number, email, phone, department, position, date_of_joining) VALUES
('John Mwamba', '123456/78/1', 'john.mwamba@hospital.com', '+260 977 123456', 'Emergency', 'Doctor', '2023-01-15'),
('Mary Banda', '234567/89/1', 'mary.banda@hospital.com', '+260 966 234567', 'Nursing', 'Nurse', '2023-03-20'),
('Peter Phiri', '345678/90/1', 'peter.phiri@hospital.com', '+260 955 345678', 'Laboratory', 'Lab Technician', '2023-06-10');
```

## 📖 Usage Guide

### 1. Logging In
1. Navigate to `http://localhost/hospital_leave_system/login.php`
2. Enter your username and password
3. Click "Login" button
4. You'll be redirected to the dashboard

### 2. Dashboard Overview
- View real-time statistics at the top
- See recent leave requests below
- Use the navigation bar to access different sections

### 3. Adding an Employee
1. Click "Add Employee" in the navigation bar
2. Fill in all required fields (marked with *)
3. Select department from dropdown
4. Choose date of joining
5. Click "Add Employee" button
6. Success message will appear, and you'll be redirected to dashboard

### 4. Viewing Employees
1. Click "View Employees" button in navigation
2. Browse the complete list of all employees
3. Use "Back to Dashboard" to return
4. Use "Manage Employees" to edit or delete records

### 5. Requesting Leave
1. Click "Request Leave" in navigation
2. Select employee from dropdown
3. Choose leave type
4. Enter start date and number of days (end date auto-calculates)
   - OR enter start date and end date (days auto-calculate)
5. Provide reason for leave
6. Click "Submit Leave Request"

### 6. Man
