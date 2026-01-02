<?php
require_once 'config.php';
require_once 'functions.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Employees</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            padding: 30px;
        }

        .container h2 {
            color: #667eea;
            margin-bottom: 25px;
            font-size: 2em;
        }

        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            margin-bottom: 30px;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        table.data-table thead {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        table.data-table th,
        table.data-table td {
            padding: 15px;
            text-align: left;
        }

        table.data-table tbody tr {
            border-bottom: 1px solid #e9ecef;
        }

        table.data-table tbody tr:hover {
            background: #f8f9fa;
        }

        .btn {
            padding: 8px 16px;
            border: none;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
            box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
            text-decoration: none;
            display: inline-block;
            margin: 2px;
            font-size: 14px;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 30px;
            border: none;
            width: 90%;
            max-width: 500px;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }

        .modal-content h3 {
            color: #667eea;
            margin-bottom: 20px;
            font-size: 1.8em;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            line-height: 20px;
        }

        .close:hover { 
            color: #667eea;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 600;
        }

        .form-group input, 
        .form-group select, 
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s;
            box-sizing: border-box;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #667eea;
        }

        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 25px;
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
            color: white;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            display: none;
            z-index: 2000;
            font-weight: 600;
        }

        .notification.error {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }

        .notification.warning {
            background: linear-gradient(135deg, #ffa726 0%, #fb8c00 100%);
        }

        .status-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
        }

        .status-pending { 
            background-color: #fff3cd; 
            color: #856404; 
        }

        .status-approved { 
            background-color: #d4edda; 
            color: #155724; 
        }

        .status-rejected { 
            background-color: #f8d7da; 
            color: #721c24; 
        }

        .status-completed { 
            background-color: #d1ecf1; 
            color: #0c5460; 
        }

        .action-buttons {
            display: flex;
            gap: 5px;
            flex-wrap: wrap;
        }

        table.data-table td button {
            margin: 0;
        }
    </style>
</head>
<body>

<div id="notification" class="notification"></div>

<div class="container">
    <h2>⚙️ Manage Employees</h2>

    <table class="data-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>NRC</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="employeeTable"></tbody>
    </table>

    <h2 style="margin-top:40px;">📋 Manage Leave Requests</h2>

    <table class="data-table">
        <thead>
            <tr>
                <th>Employee</th>
                <th>Leave Type</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Reason</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="leaveTable"></tbody>
    </table>

    <a href="index.php" class="btn">⬅ Back to Dashboard</a>
    <a href="View_employees.php" class="btn">⬅ Back to Directory</a>
    <a href="Login.php" class="btn">Logout</a>
</div>

<!-- Employee Edit Modal -->
<div id="employeeModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeEmployeeModal()">&times;</span>
        <h3 id="employeeModalTitle">Edit Employee</h3>
        <form id="employeeForm">
            <input type="hidden" id="empId" name="empId">
            
            <div class="form-group">
                <label>Full Name*</label>
                <input type="text" id="empName" name="full_name" required>
            </div>
            
            <div class="form-group">
                <label>NRC Number*</label>
                <input type="text" id="empNrc" name="nrc_number" required>
            </div>
            
            <div class="form-group">
                <label>Email*</label>
                <input type="email" id="empEmail" name="email" required>
            </div>
            
            <div class="form-group">
                <label>Phone</label>
                <input type="text" id="empPhone" name="phone">
            </div>
            
            <div class="form-group">
                <label>Department</label>
                <input type="text" id="empDept" name="department">
            </div>
            
            <div class="form-group">
                <label>Position</label>
                <input type="text" id="empPosition" name="position">
            </div>
            
            <button type="submit" class="btn">💾 Save Changes</button>
            <button type="button" class="btn" onclick="closeEmployeeModal()">Cancel</button>
        </form>
    </div>
</div>

<!-- Leave Edit Modal -->
<div id="leaveModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeLeaveModal()">&times;</span>
        <h3 id="leaveModalTitle">Edit Leave Request</h3>
        <form id="leaveForm">
            <input type="hidden" id="leaveId" name="leaveId">
            
            <div class="form-group">
                <label>Employee</label>
                <input type="text" id="leaveEmployee" readonly style="background-color: #f0f0f0;">
            </div>
            
            <div class="form-group">
                <label>Leave Type*</label>
                <select id="leaveType" name="leave_type" required>
                    <option value="Sick Leave">Sick Leave</option>
                    <option value="Annual Leave">Annual Leave</option>
                    <option value="Casual Leave">Casual Leave</option>
                    <option value="Maternity Leave">Maternity Leave</option>
                    <option value="Paternity Leave">Paternity Leave</option>
                    <option value="Unpaid Leave">Unpaid Leave</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Start Date*</label>
                <input type="date" id="leaveStart" name="start_date" required>
            </div>
            
            <div class="form-group">
                <label>End Date*</label>
                <input type="date" id="leaveEnd" name="end_date" required>
            </div>
            
            <div class="form-group">
                <label>Reason*</label>
                <textarea id="leaveReason" name="reason" rows="3" required></textarea>
            </div>
            
            <div class="form-group">
                <label>Status</label>
                <select id="leaveStatus" name="status">
                    <option value="Pending">Pending</option>
                    <option value="Approved">Approved</option>
                    <option value="Rejected">Rejected</option>
                    <option value="Completed">Completed</option>
                </select>
            </div>
            
            <button type="submit" class="btn">💾 Save Changes</button>
            <button type="button" class="btn" onclick="closeLeaveModal()">Cancel</button>
        </form>
    </div>
</div>

<script>
/* Notification System */
function showNotification(message, type = 'success') {
    const notif = document.getElementById('notification');
    notif.textContent = message;
    notif.className = 'notification ' + (type !== 'success' ? type : '');
    notif.style.display = 'block';
    setTimeout(() => {
        notif.style.display = 'none';
    }, 3000);
}

/* Check for completed leaves */
function checkCompletedLeaves() {
    fetch("api_handler.php", {
        method: "POST",
        headers: {"Content-Type": "application/x-www-form-urlencoded"},
        body: "action=checkCompletedLeaves"
    })
    .then(res => res.json())
    .then(data => {
        if (data.completed && data.completed.length > 0) {
            data.completed.forEach(leave => {
                showNotification(`Leave completed: ${leave.employee_name} - ${leave.leave_type}`, 'warning');
            });
            loadLeaves();
        }
    });
}

/* Load Employees */
function loadEmployees() {
    fetch("api_handler.php", {
        method: "POST",
        headers: {"Content-Type": "application/x-www-form-urlencoded"},
        body: "action=getEmployees"
    })
    .then(res => res.json())
    .then(data => {
        let html = "";
        data.forEach(e => {
            html += `
                <tr>
                    <td>${e.id}</td>
                    <td>${e.full_name}</td>
                    <td>${e.nrc_number}</td>
                    <td>${e.email}</td>
                    <td>${e.phone || 'N/A'}</td>
                    <td class="action-buttons">
                        <button onclick="editEmployee(${e.id})" class="btn">✏️ Edit</button>
                        <button onclick="deleteEmployee(${e.id})" class="btn">🗑 Delete</button>
                    </td>
                </tr>
            `;
        });
        document.getElementById("employeeTable").innerHTML = html;
    });
}

/* Load Leaves */
function loadLeaves() {
    fetch("api_handler.php", {
        method: "POST",
        headers: {"Content-Type": "application/x-www-form-urlencoded"},
        body: "action=getLeaves"
    })
    .then(res => res.json())
    .then(data => {
        let html = "";
        data.forEach(l => {
            const statusClass = `status-${l.status.toLowerCase()}`;
            html += `
                <tr>
                    <td>${l.full_name}</td>
                    <td>${l.leave_type}</td>
                    <td>${l.start_date}</td>
                    <td>${l.end_date}</td>
                    <td>${l.reason}</td>
                    <td><span class="status-badge ${statusClass}">${l.status}</span></td>
                    <td class="action-buttons">
                        <button onclick="editLeave(${l.id})" class="btn">✏️ Edit</button>
                        ${l.status === 'Pending' ? `
                            <button onclick="approve(${l.id})" class="btn">✔ Approve</button>
                            <button onclick="reject(${l.id})" class="btn">❌ Reject</button>
                        ` : ''}
                        <button onclick="deleteLeave(${l.id})" class="btn">🗑 Delete</button>
                    </td>
                </tr>
            `;
        });
        document.getElementById("leaveTable").innerHTML = html;
    });
}

/* Employee CRUD Operations */
function editEmployee(id) {
    fetch("api_handler.php", {
        method: "POST",
        headers: {"Content-Type": "application/x-www-form-urlencoded"},
        body: `action=getEmployee&employeeId=${id}`
    })
    .then(res => res.json())
    .then(emp => {
        document.getElementById('empId').value = emp.id;
        document.getElementById('empName').value = emp.full_name;
        document.getElementById('empNrc').value = emp.nrc_number;
        document.getElementById('empEmail').value = emp.email;
        document.getElementById('empPhone').value = emp.phone || '';
        document.getElementById('empDept').value = emp.department || '';
        document.getElementById('empPosition').value = emp.position || '';
        document.getElementById('employeeModal').style.display = 'block';
    });
}

function closeEmployeeModal() {
    document.getElementById('employeeModal').style.display = 'none';
    document.getElementById('employeeForm').reset();
}

document.getElementById('employeeForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    formData.append('action', 'updateEmployee');
    
    fetch("api_handler.php", {
        method: "POST",
        body: new URLSearchParams(formData)
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showNotification('Employee updated successfully!');
            closeEmployeeModal();
            loadEmployees();
        } else {
            showNotification(data.message || 'Failed to update employee', 'error');
        }
    });
});

function deleteEmployee(id) {
    if (!confirm("Are you sure you want to delete this employee?")) return;
    fetch("api_handler.php", {
        method: "POST",
        headers: {"Content-Type": "application/x-www-form-urlencoded"},
        body: `action=deleteEmployee&employeeId=${id}`
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showNotification('Employee deleted successfully!');
            loadEmployees();
        } else {
            showNotification(data.message || 'Failed to delete employee', 'error');
        }
    });
}

/* Leave CRUD Operations */
function editLeave(id) {
    fetch("api_handler.php", {
        method: "POST",
        headers: {"Content-Type": "application/x-www-form-urlencoded"},
        body: `action=getLeave&leaveId=${id}`
    })
    .then(res => res.json())
    .then(leave => {
        document.getElementById('leaveId').value = leave.id;
        document.getElementById('leaveEmployee').value = leave.full_name;
        document.getElementById('leaveType').value = leave.leave_type;
        document.getElementById('leaveStart').value = leave.start_date;
        document.getElementById('leaveEnd').value = leave.end_date;
        document.getElementById('leaveReason').value = leave.reason;
        document.getElementById('leaveStatus').value = leave.status;
        document.getElementById('leaveModal').style.display = 'block';
    });
}

function closeLeaveModal() {
    document.getElementById('leaveModal').style.display = 'none';
    document.getElementById('leaveForm').reset();
}

document.getElementById('leaveForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    formData.append('action', 'updateLeave');
    
    fetch("api_handler.php", {
        method: "POST",
        body: new URLSearchParams(formData)
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showNotification('Leave request updated successfully!');
            closeLeaveModal();
            loadLeaves();
        } else {
            showNotification(data.message || 'Failed to update leave request', 'error');
        }
    });
});

function approve(id) {
    fetch("api_handler.php", {
        method: "POST",
        headers: {"Content-Type": "application/x-www-form-urlencoded"},
        body: `action=approveLeave&leaveId=${id}`
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showNotification('Leave approved!');
            loadLeaves();
        }
    });
}

function reject(id) {
    fetch("api_handler.php", {
        method: "POST",
        headers: {"Content-Type": "application/x-www-form-urlencoded"},
        body: `action=rejectLeave&leaveId=${id}`
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showNotification('Leave rejected!', 'warning');
            loadLeaves();
        }
    });
}

function deleteLeave(id) {
    if (!confirm("Are you sure you want to delete this leave request?")) return;
    fetch("api_handler.php", {
        method: "POST",
        headers: {"Content-Type": "application/x-www-form-urlencoded"},
        body: `action=deleteLeave&leaveId=${id}`
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showNotification('Leave request deleted successfully!');
            loadLeaves();
        } else {
            showNotification(data.message || 'Failed to delete leave request', 'error');
        }
    });
}

// Initialize
loadEmployees();
loadLeaves();
checkCompletedLeaves();

// Check for completed leaves every 5 minutes
setInterval(checkCompletedLeaves, 300000);
</script>
</body>
</html>