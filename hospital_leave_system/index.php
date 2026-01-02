<?php
session_start();
require_once 'config.php';
require_once 'functions.php';

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    switch ($_POST['action']) {
        case 'addEmployee':
            echo json_encode(addEmployee($_POST));
            exit;
        case 'requestLeave':
            echo json_encode(requestLeave($_POST));
            exit;
        case 'approveLeave':
            echo json_encode(approveLeave($_POST['leaveId']));
            exit;
        case 'rejectLeave':
            echo json_encode(rejectLeave($_POST['leaveId']));
            exit;
        case 'searchEmployee':
            echo json_encode(searchEmployee($_POST['query']));
            exit;
        case 'getDashboardData':
            echo json_encode(getDashboardData());
            exit;
        case 'getEmployees':
            echo json_encode(getEmployees());
            exit;
        case 'getLeaves':
            echo json_encode(getLeaves());
            exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hospital Leave Management System</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🏥 Chilenje Hospital Leave Management System</h1>
            <p>Manage employee leave requests efficiently</p>
        </div>

        <div class="navbar">
            <div class="nav-buttons">
                <button onclick="showSection('dashboard')" class="active" id="btn-dashboard">Dashboard</button>
                <button onclick="showSection('addEmployee')" id="btn-addEmployee">Add Employee</button>
                <button onclick="showSection('requestLeave')" id="btn-requestLeave">Request Leave</button>
                <button onclick="showSection('manageLeaves')" id="btn-manageLeaves">Manage Leaves</button>
                <button onclick="location.href='view_employees.php'">View Employees</button>
            </div>
            <div class="search-box">
                <input type="text" id="searchInput" placeholder="Search by Name or NRC Number...">
                <button onclick="searchEmployee()">Search</button>
            </div>
        </div>

        <div class="content">
            <!-- Dashboard Section -->
            <div id="dashboard" class="section active">
                <h2>📊 Dashboard</h2>
                <div id="dashboardStats">
                    <div class="stat-card stat-purple">
                        <h3 id="totalEmployees">0</h3>
                        <p>Total Employees</p>
                    </div>
                    <div class="stat-card stat-pink">
                        <h3 id="pendingLeaves">0</h3>
                        <p>Pending Leaves</p>
                    </div>
                    <div class="stat-card stat-blue">
                        <h3 id="approvedLeaves">0</h3>
                        <p>Approved Leaves</p>
                    </div>
                    <div class="stat-card stat-green">
                        <h3 id="activeLeaves">0</h3>
                        <p>Active Leaves</p>
                    </div>
                </div>

                <h3 style="margin-top: 30px;">Recent Leave Requests</h3>
                <div id="recentLeaves"></div>
            </div>

            <!-- Add Employee Section -->
            <div id="addEmployee" class="section">
                <h2>➕ Add New Employee</h2>
                <div id="addEmployeeMessage" class="message"></div>
                <form id="addEmployeeForm">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Full Name *</label>
                            <input type="text" name="fullName" required>
                        </div>
                        <div class="form-group">
                            <label>NRC Number *</label>
                            <input type="text" name="nrcNumber" required placeholder="e.g., 123456/78/9">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Email *</label>
                            <input type="email" name="email" required>
                        </div>
                        <div class="form-group">
                            <label>Phone Number *</label>
                            <input type="tel" name="phone" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Department *</label>
                            <select name="department" required>
                                <option value="">Select Department</option>
                                <option value="Emergency">Emergency</option>
                                <option value="Surgery">Surgery</option>
                                <option value="Pediatrics">Pediatrics</option>
                                <option value="Radiology">Radiology</option>
                                <option value="Laboratory">Laboratory</option>
                                <option value="Pharmacy">Pharmacy</option>
                                <option value="Administration">Administration</option>
                                <option value="Nursing">Nursing</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Position *</label>
                            <input type="text" name="position" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Date of Joining *</label>
                        <input type="date" name="dateOfJoining" required>
                    </div>
                    <button type="submit" class="btn">Add Employee</button>
                    <button type="button" onclick="location.href='Login.php'" class="btn">Logout</button>
                </form>
            </div>

            <!-- Request Leave Section -->
            <div id="requestLeave" class="section">
                <h2>📝 Request Leave</h2>
                <div id="requestLeaveMessage" class="message"></div>
                <form id="requestLeaveForm">
                    <div class="form-group">
                        <label>Select Employee *</label>
                        <select name="employeeId" id="employeeSelect" required>
                            <option value="">Choose Employee</option>
                        </select>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Leave Type *</label>
                            <select name="leaveType" required>
                                <option value="">Select Leave Type</option>
                                <option value="Annual Leave">Annual Leave</option>
                                <option value="Sick Leave">Sick Leave</option>
                                <option value="Maternity Leave">Maternity Leave</option>
                                <option value="Paternity Leave">Paternity Leave</option>
                                <option value="Emergency Leave">Emergency Leave</option>
                                <option value="Unpaid Leave">Unpaid Leave</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Number of Days *</label>
                            <input type="number" name="numberOfDays" min="1" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Start Date *</label>
                            <input type="date" name="startDate" required>
                        </div>
                        <div class="form-group">
                            <label>End Date *</label>
                            <input type="date" name="endDate" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Reason *</label>
                        <textarea name="reason" rows="4" required></textarea>
                    </div>
                    <button type="submit" class="btn">Submit Leave Request</button>
                    <button type="button" onclick="location.href='Login.php'" class="btn">Logout</button>
                </form>
            </div>

            <!-- Manage Leaves Section -->
            <div id="manageLeaves" class="section">
                <h2>📋 Manage Leave Requests</h2>
                <div id="manageLeavesTable"></div>
            </div>

            <!-- Search Results Section -->
            <div id="searchResults" class="section">
                <h2>🔍 Search Results</h2>
                <div id="searchResultsContent"></div>
            </div>
        </div>
    </div>

    <script src="script.js"></script>
    <script>
        // Ensure dashboard loads on page refresh
        document.addEventListener('DOMContentLoaded', function() {
            showSection('dashboard');
            
            // Auto-calculate dates and days in Request Leave form
            const startDateInput = document.querySelector('#requestLeaveForm input[name="startDate"]');
            const endDateInput = document.querySelector('#requestLeaveForm input[name="endDate"]');
            const numberOfDaysInput = document.querySelector('#requestLeaveForm input[name="numberOfDays"]');
            
            // When start date and number of days change, calculate end date
            function calculateEndDate() {
                if (startDateInput.value && numberOfDaysInput.value) {
                    const startDate = new Date(startDateInput.value);
                    const days = parseInt(numberOfDaysInput.value);
                    const endDate = new Date(startDate);
                    endDate.setDate(startDate.getDate() + days - 1);
                    endDateInput.value = endDate.toISOString().split('T')[0];
                }
            }
            
            // When start date and end date change, calculate number of days
            function calculateNumberOfDays() {
                if (startDateInput.value && endDateInput.value) {
                    const startDate = new Date(startDateInput.value);
                    const endDate = new Date(endDateInput.value);
                    const diffTime = endDate - startDate;
                    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
                    if (diffDays > 0) {
                        numberOfDaysInput.value = diffDays;
                    }
                }
            }
            
            startDateInput.addEventListener('change', function() {
                if (numberOfDaysInput.value) {
                    calculateEndDate();
                } else if (endDateInput.value) {
                    calculateNumberOfDays();
                }
            });
            
            numberOfDaysInput.addEventListener('input', calculateEndDate);
            
            endDateInput.addEventListener('change', calculateNumberOfDays);
        });
    </script>
</body>
</html>