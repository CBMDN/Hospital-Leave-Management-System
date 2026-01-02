<?php
require_once 'config.php';

// Get database connection
$conn = getDBConnection();

// Set header for JSON response
header('Content-Type: application/json');

// Check if action is set
if (!isset($_POST['action'])) {
    echo json_encode(['success' => false, 'message' => 'No action specified']);
    exit;
}

$action = $_POST['action'];

// ==================== EMPLOYEE OPERATIONS ====================

// Get single employee
if ($action === 'getEmployee') {
    $id = intval($_POST['employeeId']);
    $stmt = $conn->prepare("SELECT * FROM employees WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    echo json_encode($result->fetch_assoc());
    exit;
}

// Get all employees
if ($action === 'getEmployees') {
    $stmt = $conn->prepare("SELECT * FROM employees ORDER BY id DESC");
    $stmt->execute();
    $result = $stmt->get_result();
    $employees = [];
    while ($row = $result->fetch_assoc()) {
        $employees[] = $row;
    }
    echo json_encode($employees);
    exit;
}

// Update employee
if ($action === 'updateEmployee') {
    $id = intval($_POST['empId']);
    $name = $_POST['full_name'];
    $nrc = $_POST['nrc_number'];
    $email = $_POST['email'];
    $phone = $_POST['phone'] ?? '';
    $dept = $_POST['department'] ?? '';
    $position = $_POST['position'] ?? '';
    
    $stmt = $conn->prepare("UPDATE employees SET full_name=?, nrc_number=?, email=?, phone=?, department=?, position=? WHERE id=?");
    $stmt->bind_param("ssssssi", $name, $nrc, $email, $phone, $dept, $position, $id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Employee updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => $stmt->error]);
    }
    exit;
}

// Delete employee
if ($action === 'deleteEmployee') {
    $id = intval($_POST['employeeId']);
    
    // Check if employee has leave requests
    $checkStmt = $conn->prepare("SELECT COUNT(*) as count FROM leaves WHERE employee_id = ?");
    $checkStmt->bind_param("i", $id);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    $checkData = $checkResult->fetch_assoc();
    
    if ($checkData['count'] > 0) {
        // Delete related leave requests first (CASCADE should handle this, but being explicit)
        $deleteLeaves = $conn->prepare("DELETE FROM leaves WHERE employee_id = ?");
        $deleteLeaves->bind_param("i", $id);
        $deleteLeaves->execute();
    }
    
    $stmt = $conn->prepare("DELETE FROM employees WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Employee deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => $stmt->error]);
    }
    exit;
}

// ==================== LEAVE REQUEST OPERATIONS ====================

// Get single leave request
if ($action === 'getLeave') {
    $id = intval($_POST['leaveId']);
    $stmt = $conn->prepare("SELECT l.*, e.full_name FROM leaves l JOIN employees e ON l.employee_id = e.id WHERE l.id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    echo json_encode($result->fetch_assoc());
    exit;
}

// Get all leave requests
if ($action === 'getLeaves') {
    $stmt = $conn->prepare("SELECT l.*, e.full_name FROM leaves l JOIN employees e ON l.employee_id = e.id ORDER BY l.id DESC");
    $stmt->execute();
    $result = $stmt->get_result();
    $leaves = [];
    while ($row = $result->fetch_assoc()) {
        $leaves[] = $row;
    }
    echo json_encode($leaves);
    exit;
}

// Update leave request
if ($action === 'updateLeave') {
    $id = intval($_POST['leaveId']);
    $type = $_POST['leave_type'];
    $start = $_POST['start_date'];
    $end = $_POST['end_date'];
    $reason = $_POST['reason'];
    $status = $_POST['status'];
    
    // Validate dates
    if (strtotime($end) < strtotime($start)) {
        echo json_encode(['success' => false, 'message' => 'End date cannot be before start date']);
        exit;
    }
    
    // Calculate number of days
    $datetime1 = new DateTime($start);
    $datetime2 = new DateTime($end);
    $interval = $datetime1->diff($datetime2);
    $number_of_days = $interval->days + 1; // +1 to include both start and end date
    
    // Update approved_date if status is being changed to Approved
    if ($status === 'Approved') {
        $stmt = $conn->prepare("UPDATE leaves SET leave_type=?, number_of_days=?, start_date=?, end_date=?, reason=?, status=?, approved_date=CURDATE() WHERE id=?");
        $stmt->bind_param("sissssi", $type, $number_of_days, $start, $end, $reason, $status, $id);
    } else {
        $stmt = $conn->prepare("UPDATE leaves SET leave_type=?, number_of_days=?, start_date=?, end_date=?, reason=?, status=? WHERE id=?");
        $stmt->bind_param("sissssi", $type, $number_of_days, $start, $end, $reason, $status, $id);
    }
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Leave request updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => $stmt->error]);
    }
    exit;
}

// Approve leave
if ($action === 'approveLeave') {
    $id = intval($_POST['leaveId']);
    $stmt = $conn->prepare("UPDATE leaves SET status='Approved', approved_date=CURDATE() WHERE id=?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Leave approved']);
    } else {
        echo json_encode(['success' => false, 'message' => $stmt->error]);
    }
    exit;
}

// Reject leave
if ($action === 'rejectLeave') {
    $id = intval($_POST['leaveId']);
    $stmt = $conn->prepare("UPDATE leaves SET status='Rejected' WHERE id=?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Leave rejected']);
    } else {
        echo json_encode(['success' => false, 'message' => $stmt->error]);
    }
    exit;
}

// Delete leave request
if ($action === 'deleteLeave') {
    $id = intval($_POST['leaveId']);
    $stmt = $conn->prepare("DELETE FROM leaves WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Leave request deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => $stmt->error]);
    }
    exit;
}

// ==================== AUTO-COMPLETION CHECK ====================
// Check for completed leaves
if ($action === 'checkCompletedLeaves') {
    $today = date('Y-m-d');
    
    // Find leaves that ended today or before and are still "Approved"
    $stmt = $conn->prepare("SELECT l.id, l.leave_type, e.full_name as employee_name FROM leaves l JOIN employees e ON l.employee_id = e.id WHERE l.end_date <= ? AND l.status = 'Approved'");
    $stmt->bind_param("s", $today);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $completed = [];
    while ($row = $result->fetch_assoc()) {
        $completed[] = $row;
        // Auto-update to completed
        $updateStmt = $conn->prepare("UPDATE leaves SET status='Completed' WHERE id=?");
        $updateStmt->bind_param("i", $row['id']);
        $updateStmt->execute();
    }
    
    echo json_encode(['success' => true, 'completed' => $completed]);
    exit;
}
?>