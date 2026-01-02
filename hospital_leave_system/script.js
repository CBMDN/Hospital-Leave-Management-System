// Show section
function showSection(sectionId) {
    document.querySelectorAll('.section').forEach(s => s.classList.remove('active'));
    document.querySelectorAll('.nav-buttons button').forEach(b => b.classList.remove('active'));
    
    document.getElementById(sectionId).classList.add('active');
    document.getElementById('btn-' + sectionId).classList.add('active');

    if (sectionId === 'dashboard') updateDashboard();
    if (sectionId === 'requestLeave') loadEmployees();
    if (sectionId === 'manageLeaves') displayManageLeaves();
}

// Add Employee Form Handler
document.getElementById('addEmployeeForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const formData = new FormData(e.target);
    formData.append('action', 'addEmployee');

    const response = await fetch('index.php', {
        method: 'POST',
        body: formData
    });

    const result = await response.json();
    showMessage('addEmployeeMessage', result.message, result.success ? 'success' : 'error');
    
    if (result.success) {
        e.target.reset();
        updateDashboard();
    }
});

// Request Leave Form Handler
document.getElementById('requestLeaveForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const formData = new FormData(e.target);
    formData.append('action', 'requestLeave');

    const response = await fetch('index.php', {
        method: 'POST',
        body: formData
    });

    const result = await response.json();
    showMessage('requestLeaveMessage', result.message, result.success ? 'success' : 'error');
    
    if (result.success) {
        e.target.reset();
        updateDashboard();
    }
});

// Load employees for dropdown
async function loadEmployees() {
    const formData = new FormData();
    formData.append('action', 'getEmployees');

    const response = await fetch('index.php', {
        method: 'POST',
        body: formData
    });

    const employees = await response.json();
    const select = document.getElementById('employeeSelect');
    select.innerHTML = '<option value="">Choose Employee</option>';
    
    employees.forEach(emp => {
        select.innerHTML += `<option value="${emp.id}">${emp.full_name} (${emp.nrc_number})</option>`;
    });
}

// Update Dashboard
async function updateDashboard() {
    const formData = new FormData();
    formData.append('action', 'getDashboardData');

    const response = await fetch('index.php', {
        method: 'POST',
        body: formData
    });

    const data = await response.json();
    
    document.getElementById('totalEmployees').textContent = data.totalEmployees;
    document.getElementById('pendingLeaves').textContent = data.pendingLeaves;
    document.getElementById('approvedLeaves').textContent = data.approvedLeaves;
    document.getElementById('activeLeaves').textContent = data.activeLeaves;

    displayRecentLeaves(data.recentLeaves);
}

function loadEmployeeLeaveDirectory() {
    fetch("", {
        method: "POST",
        headers: {"Content-Type": "application/x-www-form-urlencoded"},
        body: "action=getLeaves"
    })
    .then(res => res.json())
    .then(data => {
        let html = "";
        data.forEach(row => {
            html += `
                <tr>
                    <td>${row.employee_id}</td>
                    <td>${row.full_name}</td>
                    <td>${row.nrc_number}</td>
                    <td>${row.email}</td>
                    <td>${row.leave_type}</td>
                    <td>${row.reason}</td>
                    <td>${row.status}</td>
                </tr>
            `;
        });
        document.getElementById("employeeLeaveTable").innerHTML = html;
    });
}

// Display Recent Leaves
function displayRecentLeaves(leaves) {
    const container = document.getElementById('recentLeaves');

    if (leaves.length === 0) {
        container.innerHTML = '<p style="color: #718096; margin-top: 20px;">No leave requests yet.</p>';
        return;
    }

    let html = '<table><thead><tr><th>Employee</th><th>Leave Type</th><th>Duration</th><th>Status</th><th>Dates</th></tr></thead><tbody>';
    
    leaves.forEach(leave => {
        html += `
            <tr>
                <td>${leave.employee_name}</td>
                <td>${leave.leave_type}</td>
                <td>${leave.number_of_days} days</td>
                <td><span class="status-badge status-${leave.status.toLowerCase()}">${leave.status}</span></td>
                <td>${leave.start_date} to ${leave.end_date}</td>
            </tr>
        `;
    });
    
    html += '</tbody></table>';
    container.innerHTML = html;
}

// Display Manage Leaves
async function displayManageLeaves() {
    const formData = new FormData();
    formData.append('action', 'getLeaves');

    const response = await fetch('index.php', {
        method: 'POST',
        body: formData
    });

    const leaves = await response.json();
    const container = document.getElementById('manageLeavesTable');

    if (leaves.length === 0) {
        container.innerHTML = '<p style="color: #718096; margin-top: 20px;">No leave requests to manage.</p>';
        return;
    }

    let html = '<table><thead><tr><th>Employee</th><th>Type</th><th>Days</th><th>Period</th><th>Status</th><th>Requested</th><th>Actions</th></tr></thead><tbody>';
    
    leaves.forEach(leave => {
        const statusInfo = leave.approved_date ? `<br><small>Granted: ${leave.approved_date}</small>` : '';
        html += `
            <tr>
                <td>${leave.employee_name}</td>
                <td>${leave.leave_type}</td>
                <td>${leave.number_of_days}</td>
                <td>${leave.start_date}<br>to ${leave.end_date}</td>
                <td><span class="status-badge status-${leave.status.toLowerCase()}">${leave.status}</span>${statusInfo}</td>
                <td>${leave.request_date}</td>
                <td>
                    ${leave.status === 'Pending' ? `
                        <button class="action-btn btn-success" onclick="approveLeave(${leave.id})">Approve</button>
                        <button class="action-btn btn-danger" onclick="rejectLeave(${leave.id})">Reject</button>
                    ` : '-'}
                </td>
            </tr>
        `;
    });
    
    html += '</tbody></table>';
    container.innerHTML = html;
}

// Approve Leave
async function approveLeave(leaveId) {
    if (!confirm('Are you sure you want to approve this leave request?')) {
        return;
    }

    const formData = new FormData();
    formData.append('action', 'approveLeave');
    formData.append('leaveId', leaveId);

    const response = await fetch('index.php', {
        method: 'POST',
        body: formData
    });

    const result = await response.json();
    alert(result.message);
    
    if (result.success) {
        displayManageLeaves();
        updateDashboard();
    }
}

// Reject Leave
async function rejectLeave(leaveId) {
    if (!confirm('Are you sure you want to reject this leave request?')) {
        return;
    }

    const formData = new FormData();
    formData.append('action', 'rejectLeave');
    formData.append('leaveId', leaveId);

    const response = await fetch('index.php', {
        method: 'POST',
        body: formData
    });

    const result = await response.json();
    alert(result.message);
    
    if (result.success) {
        displayManageLeaves();
        updateDashboard();
    }
}

// Search Employee
async function searchEmployee() {
    const query = document.getElementById('searchInput').value.trim();
    if (!query) {
        alert('Please enter a search term');
        return;
    }

    const formData = new FormData();
    formData.append('action', 'searchEmployee');
    formData.append('query', query);

    const response = await fetch('index.php', {
        method: 'POST',
        body: formData
    });

    const results = await response.json();
    const container = document.getElementById('searchResultsContent');
    
    if (results.length === 0) {
        container.innerHTML = '<p style="color: #718096;">No employees found matching your search.</p>';
    } else {
        let html = '';
        results.forEach(emp => {
            html += `
                <div class="employee-card">
                    <h3>${emp.full_name}</h3>
                    <p><strong>NRC:</strong> ${emp.nrc_number}</p>
                    <p><strong>Department:</strong> ${emp.department} | <strong>Position:</strong> ${emp.position}</p>
                    <p><strong>Email:</strong> ${emp.email} | <strong>Phone:</strong> ${emp.phone}</p>
                    <p><strong>Joined:</strong> ${emp.date_of_joining}</p>
                    <h4 style="margin-top: 15px;">Leave History (${emp.leaves.length} requests)</h4>
            `;
            
            if (emp.leaves.length > 0) {
                html += '<table style="margin-top: 10px;"><thead><tr><th>Type</th><th>Period</th><th>Days</th><th>Status</th></tr></thead><tbody>';
                emp.leaves.forEach(leave => {
                    html += `
                        <tr>
                            <td>${leave.leave_type}</td>
                            <td>${leave.start_date} to ${leave.end_date}</td>
                            <td>${leave.number_of_days}</td>
                            <td><span class="status-badge status-${leave.status.toLowerCase()}">${leave.status}</span></td>
                        </tr>
                    `;
                });
                html += '</tbody></table>';
            } else {
                html += '<p style="color: #718096; margin-top: 10px;">No leave requests yet.</p>';
            }
            
            html += '</div>';
        });
        container.innerHTML = html;
    }

    showSection('searchResults');
}

// EDIT LEAVE (Fetch data into modal/form)
function editLeave(id) {
    $.ajax({
        url: "function.php",
        type: "POST",
        data: { action: "get_leave", id: id },
        dataType: "json",
        success: function (data) {
            $("#leave_id").val(data.id);
            $("#employee_name").val(data.employee_name);
            $("#leave_type").val(data.leave_type);
            $("#start_date").val(data.start_date);
            $("#end_date").val(data.end_date);
            $("#reason").val(data.reason);
            $("#editLeaveModal").modal("show");
        }
    });
}

// UPDATE LEAVE
$("#updateLeaveForm").on("submit", function (e) {
    e.preventDefault();

    $.ajax({
        url: "function.php",
        type: "POST",
        data: $(this).serialize() + "&action=update_leave",
        success: function (response) {
            alert(response);
            location.reload();
        }
    });
});

// DELETE LEAVE
function deleteLeave(id) {
    if (confirm("Are you sure you want to delete this leave?")) {
        $.ajax({
            url: "function.php",
            type: "POST",
            data: { action: "delete_leave", id: id },
            success: function (response) {
                alert(response);
                location.reload();
            }
        });
    }
}

// AUTO UPDATE STATUS TO DONE (runs daily or on page load)
$(document).ready(function () {
    $.ajax({
        url: "function.php",
        type: "POST",
        data: { action: "auto_update_status" }
    });
});

function loadEmployees() {
    fetch("index.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "action=getEmployees"
    })
    .then(response => response.json())
    .then(data => {
        let options = '<option value="">Choose Employee</option>';
        data.forEach(emp => {
            options += `<option value="${emp.id}">
                ${emp.full_name} (${emp.nrc_number})
            </option>`;
        });
        document.getElementById("employeeSelect").innerHTML = options;
    });
}
document.addEventListener("DOMContentLoaded", () => {
    loadEmployees();
});

document.getElementById("addEmployeeForm").addEventListener("submit", function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    formData.append("action", "addEmployee");

    fetch("index.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.json())
    .then(response => {
        if (response.success) {
            this.reset();
            loadEmployees(); // 🔥 AUTO REFRESH EMPLOYEE LIST
            alert("Employee added successfully");
        } else {
            alert(response.message || "Error adding employee");
        }
    });
});
// Show message
function showMessage(elementId, message, type) {
    const msgDiv = document.getElementById(elementId);
    msgDiv.textContent = message;
    msgDiv.className = `message ${type}`;
    msgDiv.style.display = 'block';
    setTimeout(() => {
        msgDiv.style.display = 'none';
    }, 5000);
}

// Initialize on load
document.addEventListener('DOMContentLoaded', function() {
    updateDashboard();
});