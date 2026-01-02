<?php
/**
 * Backend Functions File
 * 
 * This file contains all PHP functions for the Hospital Leave Management System
 */

require_once 'config.php';

/**
 * Add New Employee
 * 
 * @param array $data Employee data from form
 * @return array Success status and message
 */
function addEmployee($data) {
    $conn = getDBConnection();
    
    // Sanitize input data
    $fullName = $conn->real_escape_string(trim($data['fullName']));
    $nrcNumber = $conn->real_escape_string(trim($data['nrcNumber']));
    $email = $conn->real_escape_string(trim($data['email']));
    $phone = $conn->real_escape_string(trim($data['phone']));
    $department = $conn->real_escape_string(trim($data['department']));
    $position = $conn->real_escape_string(trim($data['position']));
    $dateOfJoining = $conn->real_escape_string($data['dateOfJoining']);
    
    // Check if NRC number already exists
    $checkSql = "SELECT id FROM employees WHERE nrc_number = '$nrcNumber'";
    $result = $conn->query($checkSql);
    
    if ($result->num_rows > 0) {
        $conn->close();
        return ['success' => false, 'message' => 'Error: NRC number already exists!'];
    }
    
    // Insert new employee
    $sql = "INSERT INTO employees (full_name, nrc_number, email, phone, department, position, date_of_joining) 
            VALUES ('$fullName', '$nrcNumber', '$email', '$phone', '$department', '$position', '$dateOfJoining')";
    
    if ($conn->query($sql)) {
        $conn->close();
        return ['success' => true, 'message' => 'Employee added successfully!'];
    } else {
        $error = $conn->error;
        $conn->close();
        return ['success' => false, 'message' => 'Error: ' . $error];
    }
}

/**
 * Request Leave
 * 
 * @param array $data Leave request data from form
 * @return array Success status and message
 */
function requestLeave($data) {
    $conn = getDBConnection();
    
    // Sanitize input data
    $employeeId = (int)$data['employeeId'];
    $leaveType = $conn->real_escape_string(trim($data['leaveType']));
    $numberOfDays = (int)$data['numberOfDays'];
    $startDate = $conn->real_escape_string($data['startDate']);
    $endDate = $conn->real_escape_string($data['endDate']);
    $reason = $conn->real_escape_string(trim($data['reason']));
    $requestDate = date('Y-m-d');
    
    // Validate dates
    if (strtotime($endDate) < strtotime($startDate)) {
        $conn->close();
        return ['success' => false, 'message' => 'Error: End date cannot be before start date!'];
    }
    
    // Check if employee exists
    $checkSql = "SELECT id FROM employees WHERE id = $employeeId";
    $result = $conn->query($checkSql);
    
    if ($result->num_rows === 0) {
        $conn->close();
        return ['success' => false, 'message' => 'Error: Employee not found!'];
    }
    
    // Insert leave request
    $sql = "INSERT INTO leaves (employee_id, leave_type, number_of_days, start_date, end_date, reason, request_date) 
            VALUES ($employeeId, '$leaveType', $numberOfDays, '$startDate', '$endDate', '$reason', '$requestDate')";
    
    if ($conn->query($sql)) {
        $conn->close();
        return ['success' => true, 'message' => 'Leave request submitted successfully!'];
    } else {
        $error = $conn->error;
        $conn->close();
        return ['success' => false, 'message' => 'Error: ' . $error];
    }
}

/**
 * Approve Leave Request
 * 
 * @param int $leaveId Leave ID to approve
 * @return array Success status and message
 */
function approveLeave($leaveId) {
    $conn = getDBConnection();
    $leaveId = (int)$leaveId;
    $approvedDate = date('Y-m-d');
    
    // Check if leave exists and is pending
    $checkSql = "SELECT status FROM leaves WHERE id = $leaveId";
    $result = $conn->query($checkSql);
    
    if ($result->num_rows === 0) {
        $conn->close();
        return ['success' => false, 'message' => 'Error: Leave request not found!'];
    }
    
    $leave = $result->fetch_assoc();
    if ($leave['status'] !== 'Pending') {
        $conn->close();
        return ['success' => false, 'message' => 'Error: This leave request has already been processed!'];
    }
    
    // Update leave status
    $sql = "UPDATE leaves SET status = 'Approved', approved_date = '$approvedDate' WHERE id = $leaveId";
    
    if ($conn->query($sql)) {
        $conn->close();
        return ['success' => true, 'message' => 'Leave approved successfully!'];
    } else {
        $error = $conn->error;
        $conn->close();
        return ['success' => false, 'message' => 'Error: ' . $error];
    }
}

/**
 * Reject Leave Request
 * 
 * @param int $leaveId Leave ID to reject
 * @return array Success status and message
 */
function rejectLeave($leaveId) {
    $conn = getDBConnection();
    $leaveId = (int)$leaveId;
    
    // Check if leave exists and is pending
    $checkSql = "SELECT status FROM leaves WHERE id = $leaveId";
    $result = $conn->query($checkSql);
    
    if ($result->num_rows === 0) {
        $conn->close();
        return ['success' => false, 'message' => 'Error: Leave request not found!'];
    }
    
    $leave = $result->fetch_assoc();
    if ($leave['status'] !== 'Pending') {
        $conn->close();
        return ['success' => false, 'message' => 'Error: This leave request has already been processed!'];
    }
    
    // Update leave status
    $sql = "UPDATE leaves SET status = 'Rejected' WHERE id = $leaveId";
    
    if ($conn->query($sql)) {
        $conn->close();
        return ['success' => true, 'message' => 'Leave rejected successfully.'];
    } else {
        $error = $conn->error;
        $conn->close();
        return ['success' => false, 'message' => 'Error: ' . $error];
    }
}

/**
 * Get All Employees
 * 
 * @return array List of all employees
 */
function getEmployees() {
    $conn = getDBConnection();
    $sql = "SELECT * FROM employees ORDER BY full_name ASC";
    $result = $conn->query($sql);
    
    $employees = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $employees[] = $row;
        }
    }
    
    $conn->close();
    return $employees;
}

/**
 * Get All Leave Requests
 * 
 * @return array List of all leave requests with employee names
 */
function getLeaves() {
    $conn = getDBConnection();
    $sql = "SELECT l.*, e.full_name as employee_name 
            FROM leaves l 
            JOIN employees e ON l.employee_id = e.id 
            ORDER BY l.created_at DESC";
    $result = $conn->query($sql);
    
    $leaves = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $leaves[] = $row;
        }
    }
    
    $conn->close();
    return $leaves;
}

/**
 * Get Dashboard Statistics
 * 
 * @return array Dashboard data including stats and recent leaves
 */
function getDashboardData() {
    $conn = getDBConnection();
    
    // Total employees
    $result = $conn->query("SELECT COUNT(*) as count FROM employees");
    $totalEmployees = $result->fetch_assoc()['count'];
    
    // Pending leaves
    $result = $conn->query("SELECT COUNT(*) as count FROM leaves WHERE status = 'Pending'");
    $pendingLeaves = $result->fetch_assoc()['count'];
    
    // Approved leaves
    $result = $conn->query("SELECT COUNT(*) as count FROM leaves WHERE status = 'Approved'");
    $approvedLeaves = $result->fetch_assoc()['count'];
    
    // Active leaves (approved and currently ongoing)
    $today = date('Y-m-d');
    $result = $conn->query("SELECT COUNT(*) as count FROM leaves 
                           WHERE status = 'Approved' 
                           AND start_date <= '$today' 
                           AND end_date >= '$today'");
    $activeLeaves = $result->fetch_assoc()['count'];
    
    // Recent leaves (last 5)
    $result = $conn->query("SELECT l.*, e.full_name as employee_name 
                           FROM leaves l 
                           JOIN employees e ON l.employee_id = e.id 
                           ORDER BY l.created_at DESC 
                           LIMIT 5");
    
    $recentLeaves = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $recentLeaves[] = $row;
        }
    }
    
    $conn->close();
    
    return [
        'totalEmployees' => $totalEmployees,
        'pendingLeaves' => $pendingLeaves,
        'approvedLeaves' => $approvedLeaves,
        'activeLeaves' => $activeLeaves,
        'recentLeaves' => $recentLeaves
    ];
}

/**
 * Search Employee by Name or NRC Number
 * 
 * @param string $query Search query
 * @return array List of matching employees with their leave history
 */
function searchEmployee($query) {
    $conn = getDBConnection();
    $query = $conn->real_escape_string(trim($query));
    
    // Search employees
    $sql = "SELECT * FROM employees 
            WHERE full_name LIKE '%$query%' 
            OR nrc_number LIKE '%$query%' 
            ORDER BY full_name ASC";
    
    $result = $conn->query($sql);
    
    $employees = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // Get leaves for this employee
            $empId = $row['id'];
            $leaveResult = $conn->query("SELECT * FROM leaves 
                                        WHERE employee_id = $empId 
                                        ORDER BY created_at DESC");
            
            $leaves = [];
            if ($leaveResult->num_rows > 0) {
                while ($leaveRow = $leaveResult->fetch_assoc()) {
                    $leaves[] = $leaveRow;
                }
            }
            
            $row['leaves'] = $leaves;
            $employees[] = $row;
        }
    }
    
    $conn->close();
    return $employees;
}
?>