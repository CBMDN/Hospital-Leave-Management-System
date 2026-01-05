<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

// Prevent caching
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");



// Database configuration
$servername = "localhost";
$username = "root"; // replace with your DB username
$password = "CH25800";     // replace with your DB password
$dbname = "hospital_leave_system";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all employees
$sql = "SELECT full_name, email, position, department, phone, date_of_joining FROM employees ORDER BY full_name ASC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee List</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
            margin: 0;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .header h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
            margin: 0;
        }

        .header p {
            margin: 10px 0 0 0;
            opacity: 0.95;
        }

        .content {
            padding: 30px;
        }

        .content h2 {
            color: #667eea;
            margin-bottom: 25px;
            font-size: 2em;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        table thead {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        table th,
        table td {
            padding: 15px;
            text-align: left;
        }

        table tbody tr {
            border-bottom: 1px solid #e9ecef;
        }

        table tbody tr:hover {
            background: #f8f9fa;
        }

        .nav-buttons {
            display: flex;
            gap: 10px;
            margin-top: 20px;
            justify-content: flex-start;
        }

        .nav-buttons button {
            padding: 12px 24px;
            border: none;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }

        .nav-buttons button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🏥 Hospital Employees</h1>
            <p>Complete list of all hospital staff members</p>
        </div>

        <div class="content">
            <h2>👥 Employee Directory</h2>

            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Position</th>
                        <th>Department</th>
                        <th>Phone</th>
                        <th>Date Joined</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        // Output data for each employee
                        while($row = $result->fetch_assoc()) {
                            echo "<tr>
                                    <td>" . htmlspecialchars($row['full_name']) . "</td>
                                    <td>" . htmlspecialchars($row['email']) . "</td>
                                    <td>" . htmlspecialchars($row['position']) . "</td>
                                    <td>" . htmlspecialchars($row['department']) . "</td>
                                    <td>" . htmlspecialchars($row['phone']) . "</td>
                                    <td>" . htmlspecialchars($row['date_of_joining']) . "</td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6' style='text-align: center; color: #666;'>No employees found.</td></tr>";
                    }
                    $conn->close();
                    ?>
                </tbody>
            </table>

            <div class="nav-buttons">
                <button onclick="location.href='index.php'">Back to Dashboard</button>
                <button onclick="location.href='Manage_employees.php'">Manage Employees</button>
                <button type="button" onclick="location.href='logout.php'" class="btn">Logout</button>
            </div>
        </div>
    </div>
</body>
</html><?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

// Prevent caching
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");



// Database configuration
$servername = "localhost";
$username = "root"; // replace with your DB username
$password = "CH25800";     // replace with your DB password
$dbname = "hospital_leave_system";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all employees
$sql = "SELECT full_name, email, position, department, phone, date_of_joining FROM employees ORDER BY full_name ASC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee List</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
            margin: 0;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .header h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
            margin: 0;
        }

        .header p {
            margin: 10px 0 0 0;
            opacity: 0.95;
        }

        .content {
            padding: 30px;
        }

        .content h2 {
            color: #667eea;
            margin-bottom: 25px;
            font-size: 2em;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        table thead {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        table th,
        table td {
            padding: 15px;
            text-align: left;
        }

        table tbody tr {
            border-bottom: 1px solid #e9ecef;
        }

        table tbody tr:hover {
            background: #f8f9fa;
        }

        .nav-buttons {
            display: flex;
            gap: 10px;
            margin-top: 20px;
            justify-content: flex-start;
        }

        .nav-buttons button {
            padding: 12px 24px;
            border: none;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }

        .nav-buttons button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🏥 Hospital Employees</h1>
            <p>Complete list of all hospital staff members</p>
        </div>

        <div class="content">
            <h2>👥 Employee Directory</h2>

            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Position</th>
                        <th>Department</th>
                        <th>Phone</th>
                        <th>Date Joined</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        // Output data for each employee
                        while($row = $result->fetch_assoc()) {
                            echo "<tr>
                                    <td>" . htmlspecialchars($row['full_name']) . "</td>
                                    <td>" . htmlspecialchars($row['email']) . "</td>
                                    <td>" . htmlspecialchars($row['position']) . "</td>
                                    <td>" . htmlspecialchars($row['department']) . "</td>
                                    <td>" . htmlspecialchars($row['phone']) . "</td>
                                    <td>" . htmlspecialchars($row['date_of_joining']) . "</td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6' style='text-align: center; color: #666;'>No employees found.</td></tr>";
                    }
                    $conn->close();
                    ?>
                </tbody>
            </table>

            <div class="nav-buttons">
                <button onclick="location.href='index.php'">Back to Dashboard</button>
                <button onclick="location.href='Manage_employees.php'">Manage Employees</button>
                <button type="button" onclick="location.href='logout.php'" class="btn">Logout</button>
            </div>
        </div>
    </div>
</body>
</html>
