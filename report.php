<?php
ob_start();
session_start();
if (!isset($_SESSION['name']) || $_SESSION['name'] != 'oasis') {
    header('location: login.php');
    exit();
}
include('connect.php');
$activeSection = isset($_POST['generate_mass']) ? 'mass' : (isset($_POST['generate_individual']) ? 'individual' : '');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Add Student</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="../css/main.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>table {
    background: url('images.jpeg') no-repeat center;
    background-size: cover;
}
</style>
</head>
<body>
<header>
    <div class="navbar">
        <a href="signup.php">Create Users</a>
        <a href="index.php">Add Student/Teacher</a>
        <a href="v-students.php">View Students</a>
        <a href="v-teachers.php">View Teachers</a>
        <a href="report.php">Report</a>
        <a href="../logout.php">Logout</a>
    </div>
</header>
<script>
    function showSection(section) {
        document.getElementById('massReportSection').style.display = section === 'mass' ? 'block' : 'none';
        document.getElementById('individualReportSection').style.display = section === 'individual' ? 'block' : 'none';
        document.getElementById('shortageReportSection').style.display = section === 'shortage' ? 'block' : 'none';
        document.getElementById('singleDayReportSection').style.display = section === 'singleDay' ? 'block' : 'none';
    }
    window.onload = function() {
        <?php if ($activeSection) echo "showSection('$activeSection');"; ?>
    };
</script>
<script>
    function showSection(section) {
        document.getElementById('massReportSection').style.display = section === 'mass' ? 'block' : 'none';
        document.getElementById('individualReportSection').style.display = section === 'individual' ? 'block' : 'none';
        document.getElementById('shortageReportSection').style.display = section === 'shortage' ? 'block' : 'none';
        document.getElementById('singleDayReportSection').style.display = section === 'singleDay' ? 'block' : 'none';
        document.getElementById('workingDaysSection').style.display = section === 'workingDays' ? 'block' : 'none';
    }
    window.onload = function() {
        <?php if ($activeSection) echo "showSection('$activeSection');"; ?>
    };
</script>
<div class="container text-center">
<button class="btn btn-dark" onclick="showSection('workingDays')">Working Days</button>
    <button class="btn btn-primary" onclick="showSection('mass')">Mass Report</button>
    <button class="btn btn-info" onclick="showSection('singleDay')">Single Day Report</button>
    <button class="btn btn-success" onclick="showSection('individual')">Individual Report</button>
    <button class="btn btn-warning" onclick="showSection('shortage')">Attendance Shortage</button>
    </div>
<center>
<div class="container">
<!-- WORKING DAYS REPORT -->
<div id="workingDaysSection" style="display:none;">
    <h1>Working Days Attendance</h1>
    <form method="post">
        <label>Class ID:</label>
        <input type="text" name="batch" required placeholder="Enter Class ID"><br><br>
        <input type="submit" name="generate_working_days" value="Show Working Days" class="btn btn-dark">
    </form>
    <?php
    if (isset($_POST['generate_working_days'])) {
        $batch = htmlspecialchars($_POST['batch']);
        $stmt = $conn->prepare("SELECT DISTINCT stat_date FROM attendance 
                                INNER JOIN students ON attendance.stat_id = students.st_id
                                WHERE students.st_batch = ? 
                                ORDER BY stat_date ASC");
        $stmt->bind_param("s", $batch);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            echo "<h4>Working Days for Class ID: $batch</h4>";
            echo "<table class='table table-bordered'>
                    <thead>
                        <tr>
                            <th>S.No</th>
                            <th>Date</th>
                            <th>Day</th>
                            <th>Month</th>
                        </tr>
                    </thead>
                    <tbody>";
            $serial_no = 1;
            $csv_data = "S.No,Date,Day,Month\n"; // CSV header
            while ($row = $result->fetch_assoc()) {
                $date = $row['stat_date'];
                $day = date('l', strtotime($date)); // Get day name
                $month = date('F', strtotime($date)); // Get month name
                echo "<tr>
                        <td>{$serial_no}</td>
                        <td>{$date}</td>
                        <td>{$day}</td>
                        <td>{$month}</td>
                      </tr>";
                // Append data to CSV
                $csv_data .= "$serial_no,$date,$day,$month\n";
                $serial_no++;
            }
            echo "</tbody></table>";
            // Save CSV file
            $filename = "working_days_$batch.csv";
            file_put_contents($filename, $csv_data);
            // Provide download link
            echo "<a href='$filename' class='btn btn-success' download>Download as CSV</a>";
        } else {
            echo "<p>No working days found for Class ID: $batch.</p>";
        }
    }
    ?>
</div>
    <!-- SINGLE DAY REPORT -->
    <div id="singleDayReportSection" style="display:none;">
        <h1>Single Day Attendance Report</h1>
        <form method="post">
            <label>Class ID:</label>
            <input type="text" name="class_id" required placeholder="Enter Class ID"><br><br>

            <label>Select Date:</label>
            <input type="date" name="report_date" required><br><br>

            <input type="submit" name="generate_single_day" value="Generate Report" class="btn btn-info">
        </form>
        <?php
        if (isset($_POST['generate_single_day'])) {
            $class_id = htmlspecialchars($_POST['class_id']);
            $report_date = $_POST['report_date'];

            $stmt = $conn->prepare("SELECT students.st_id, students.st_name, students.st_dept, attendance.st_status 
                                    FROM students 
                                    INNER JOIN attendance ON students.st_id = attendance.stat_id
                                    WHERE students.st_batch = ? 
                                    AND attendance.stat_date = ?");
            $stmt->bind_param("ss", $class_id, $report_date);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                echo "<h4>Attendance Report for $report_date (Class ID: $class_id)</h4>";
                echo "<table class='table table-bordered'>
                        <thead>
                            <tr>
                                <th>S.No</th>
                                <th>Reg. No.</th>
                                <th>Name</th>
                                <th>Department</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>";
                $serial_no = 1;
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>{$serial_no}</td>
                            <td>{$row['st_id']}</td>
                            <td>{$row['st_name']}</td>
                            <td>{$row['st_dept']}</td>
                            <td>{$row['st_status']}</td>
                          </tr>";
                    $serial_no++;
                }
               echo "</tbody></table>";
                echo '<form action="download.php" method="post">
                    <input type="hidden" name="type" value="singleDay">
                    <input type="hidden" name="report_date" value="' . $report_date . '">
                    <input type="hidden" name="class_id" value="' . $class_id . '">
                    <button type="submit" class="btn btn-success">Download as CSV</button>
                </form>';
            } else {
                echo "<p>No attendance records found for this class and date.</p>";
            }
        }
        ?>
    </div>
     <!-- Set Holiday Section -->
     <div id="holidaySection" style="display:none;">
        <h1>Set Holiday</h1>
        <form method="post">
            <label>Class ID:</label>
            <input type="text" name="holiday_batch" required placeholder="Enter Class ID"><br><br>
            <input type="submit" name="generate_holiday" value="Show Missing Attendance" class="btn btn-danger">
        </form>
        <?php
        if (isset($_POST['generate_holiday'])) {
            $holiday_batch = htmlspecialchars($_POST['holiday_batch']);
            // Query for missing attendance dates
            $stmt = $conn->prepare("SELECT DISTINCT stat_date FROM attendance 
                                    WHERE stat_id IN (SELECT st_id FROM students WHERE st_batch = ?) 
                                    AND st_status IS NULL 
                                    ORDER BY stat_date ASC");
            $stmt->bind_param("s", $holiday_batch);
            $stmt->execute();
            $result = $stmt->get_result();
           if ($result->num_rows > 0) {
                echo "<h4>Missing Attendance for Class ID: $holiday_batch</h4>";
                echo "<table class='table table-bordered'>
                        <thead>
                            <tr>
                                <th>S.No</th>
                                <th>Date</th>
                                <th>Day</th>
                                <th>Month</th>
                            </tr>
                        </thead>
                        <tbody>";
                $serial_no = 1;
                while ($row = $result->fetch_assoc()) {
                    $date = $row['stat_date'];
                    $day = date('l', strtotime($date)); // Get day name
                    $month = date('F', strtotime($date)); // Get month name
                    echo "<tr>
                            <td>{$serial_no}</td>
                            <td>{$date}</td>
                            <td>{$day}</td>
                            <td>{$month}</td>
                          </tr>";
                    $serial_no++;
                }
                                echo "</tbody></table>";
                // Provide a form to mark the days as holidays
                echo "<form method='post'>
                        <input type='hidden' name='holiday_batch' value='$holiday_batch'>
                        <input type='submit' name='mark_holidays' value='Mark as Holidays' class='btn btn-danger'>
                      </form>";
            } else {
                echo "<p>No missing attendance found for Class ID: $holiday_batch.</p>";
            }
        }
        // Handle marking as holiday
        if (isset($_POST['mark_holidays'])) {
            $holiday_batch = $_POST['holiday_batch'];
            // Update the attendance to mark the days as holidays
            $stmt = $conn->prepare("UPDATE attendance 
                                    SET st_status = 'Holiday' 
                                    WHERE stat_id IN (SELECT st_id FROM students WHERE st_batch = ?) 
                                    AND st_status IS NULL");
            $stmt->bind_param("s", $holiday_batch);
            if ($stmt->execute()) {
                echo "<p>Attendance has been marked as Holiday for the missing dates.</p>";
            } else {
                echo "<p>Failed to mark the attendance as Holiday.</p>";
            }
        }
        ?>
    </div>
    <!-- MASS ATTENDANCE REPORT -->
    <div id="massReportSection" style="display:none;">
        <h1>Mass Attendance Report</h1>
        <form method="post">
            <label>Select Class id:</label>
            <input type="text" name="batch" required placeholder="Enter Class id"> <br><br>
            <label>Start Date:</label>
            <input type="date" name="start_date" required><br><br>
            <label>End Date:</label>
            <input type="date" name="end_date" required><br><br>
            <input type="submit" name="generate_mass" value="Generate Report" class="btn btn-info">
        </form>
        <?php
        if (isset($_POST['generate_mass'])) {
            $batch = htmlspecialchars($_POST['batch']);
            $start_date = $_POST['start_date'];
            $end_date = $_POST['end_date'];
            $stmt = $conn->prepare("SELECT students.st_id, students.st_name, students.st_dept, 
                                           COUNT(attendance.stat_id) AS total_classes, 
                                           SUM(CASE WHEN attendance.st_status = 'Present' THEN 1 ELSE 0 END) AS present_count 
                                    FROM students 
                                    INNER JOIN attendance ON students.st_id = attendance.stat_id
                                    WHERE students.st_batch = ? 
                                    AND attendance.stat_date BETWEEN ? AND ?
                                    GROUP BY students.st_id");
            $stmt->bind_param("sss", $batch, $start_date, $end_date);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $_SESSION['mass_report'] = $result->fetch_all(MYSQLI_ASSOC);
                echo "<h4>Attendance Report from $start_date to $end_date (Batch: $batch)</h4>";
                echo "<table class='table table-bordered'>
                        <thead>
                            <tr>
                                <th>S.No</th>
                                <th>Reg. No.</th>
                                <th>Name</th>
                                <th>Department</th>
                                <th>Total Days</th>
                                <th>Present</th>
                                <th>Absent</th>
                                <th>Percentage</th>
                            </tr>
                        </thead>
                        <tbody>";
                $serial_no = 1;
                foreach ($_SESSION['mass_report'] as $row) {
                    $absent = $row['total_classes'] - $row['present_count'];
                    $percentage = round(($row['present_count'] / $row['total_classes']) * 100, 2);
                   echo "<tr>
                            <td>{$serial_no}</td>
                            <td>{$row['st_id']}</td>
                            <td>{$row['st_name']}</td>
                            <td>{$row['st_dept']}</td>
                            <td>{$row['total_classes']}</td>
                            <td>{$row['present_count']}</td>
                            <td>{$absent}</td>
                            <td>{$percentage}%</td>
                          </tr>";
                    $serial_no++;
                }
                echo "</tbody></table>";
                echo '<form action="download.php" method="post">
                        <input type="hidden" name="type" value="mass">
                        <button type="submit" class="btn btn-success">Download as CSV</button>
                      </form>';
            } else {
                echo "<p>No attendance records found for this batch and date range.</p>";
            }
        }
        ?>
    </div>
    <!-- INDIVIDUAL ATTENDANCE REPORT -->
    <div id="individualReportSection" style="display:none;">
    <h1>Individual Report</h1>
    <form method="post">
        <label>Student Reg. No:</label>
        <input type="text" name="sr_id" required><br><br>
        <input type="submit" name="generate_individual" value="Generate Report" class="btn btn-info">
    </form>
    <?php
    if (isset($_POST['generate_individual'])) {
        $sr_id = $_POST['sr_id'];
        echo "<p><strong>Entered Registration Number: </strong>$sr_id</p>"; // Display entered registration number
        $query = $conn->prepare("SELECT students.st_name, students.st_dept, COUNT(*) AS total_classes, 
                                        SUM(CASE WHEN attendance.st_status = 'Present' THEN 1 ELSE 0 END) AS present_count 
                                 FROM students 
                                 INNER JOIN attendance ON students.st_id = attendance.stat_id
                                 WHERE students.st_id = ?");
        $query->bind_param("s", $sr_id);
        $query->execute();
        $result = $query->get_result();
        $row = $result->fetch_assoc();
        if ($row['total_classes'] > 0) {
            $_SESSION['individual_report'] = [$row];
            echo "<table class='table table-bordered'>
                    <tr><td>Name:</td><td>{$row['st_name']}</td></tr>
                    <tr><td>Department:</td><td>{$row['st_dept']}</td></tr>
                    <tr><td>Total Days:</td><td>{$row['total_classes']}</td></tr>
                    <tr><td>Present:</td><td>{$row['present_count']}</td></tr>
                    <tr><td>Absent:</td><td>" . ($row['total_classes'] - $row['present_count']) . "</td></tr>
                    <tr><td>Attendance Percentage:</td><td>" . round(($row['present_count'] / $row['total_classes']) * 100, 2) . "%</td></tr>
                  </table>";
            echo '<form action="download.php" method="post">
                    <input type="hidden" name="type" value="individual">
                    <button type="submit" class="btn btn-success">Download as CSV</button>
                  </form>';
        } else {
            echo "<p>No records found.</p>";
        }
    }
    ?>
</div>
<div id="shortageReportSection" style="display:none;">
        <h1>Attendance Shortage Report</h1>
        <form method="post">
            <label>Class ID:</label>
            <input type="text" name="batch" required placeholder="Enter Class ID"><br><br>
            <label>Enter Minimum Attendance Percentage:</label>
            <input type="number" name="shortage_percentage" required placeholder="Enter percentage (e.g., 75)" min="0" max="100"> <br><br>
            <input type="submit" name="generate_shortage" value="Generate Report" class="btn btn-danger">
        </form>
        <?php
        if (isset($_POST['generate_shortage'])) {
            $batch = htmlspecialchars($_POST['batch']);
            $percentage = $_POST['shortage_percentage'];

            $stmt = $conn->prepare("SELECT students.st_id, students.st_name, students.st_dept, 
                                           COUNT(attendance.stat_id) AS total_classes, 
                                           SUM(CASE WHEN attendance.st_status = 'Present' THEN 1 ELSE 0 END) AS present_count 
                                    FROM students 
                                    INNER JOIN attendance ON students.st_id = attendance.stat_id
                                    WHERE students.st_batch = ? 
                                    GROUP BY students.st_id
                                    HAVING COUNT(attendance.stat_id) > 0 
                                    AND (SUM(CASE WHEN attendance.st_status = 'Present' THEN 1 ELSE 0 END) / COUNT(attendance.stat_id)) * 100 < ?");
            $stmt->bind_param("sd", $batch, $percentage);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $_SESSION['shortage_report'] = $result->fetch_all(MYSQLI_ASSOC);
                echo "<h4>Students Below $percentage% Attendance (Class ID: $batch)</h4>";
                echo "<table class='table table-bordered'>
                        <thead>
                            <tr>
                                <th>S.No</th>
                                <th>Reg. No.</th>
                                <th>Name</th>
                                <th>Department</th>
                                <th>Total Days</th>
                                <th>Present</th>
                                <th>Absent</th>
                                <th>Percentage</th>
                            </tr>
                        </thead>
                        <tbody>";
                $serial_no = 1;
                foreach ($_SESSION['shortage_report'] as $row) {
                    $absent = $row['total_classes'] - $row['present_count'];
                    $percentage_attended = round(($row['present_count'] / $row['total_classes']) * 100, 2);
                    echo "<tr>
                            <td>{$serial_no}</td>
                            <td>{$row['st_id']}</td>
                            <td>{$row['st_name']}</td>
                            <td>{$row['st_dept']}</td>
                            <td>{$row['total_classes']}</td>
                            <td>{$row['present_count']}</td>
                            <td>{$absent}</td>
                            <td>{$percentage_attended}%</td>
                          </tr>";
                    $serial_no++;
                }
                echo "</tbody></table>";
                echo '<form action="download.php" method="post">
                        <input type="hidden" name="type" value="shortage">
                        <button type="submit" class="btn btn-success">Download as CSV</button>
                      </form>';
            } else {
                echo "<p>No students found below $percentage% attendance in Class ID: $batch.</p>";
            }
        }
        ?>
    </div>
</div>
</div>
</center>
</body>
</html>

