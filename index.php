<?php
ob_start();
session_start();

if ($_SESSION['name'] != 'oasis') {
    header('location: login.php');
    exit();
}

include('connect.php');

$success_msg = "";
$error_msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['std'])) { // Save Student
        $st_id = $_POST['st_id'];
        $st_name = $_POST['st_name'];
        $st_dept = $_POST['st_dept'];
        $st_batch = $_POST['st_batch'];
        $st_sem = $_POST['st_sem'];
        $st_email = $_POST['st_email'];

        // Check if student ID already exists
        $check_query = "SELECT * FROM students WHERE st_id = '$st_id'";
        $result = mysqli_query($conn, $check_query);

        if (mysqli_num_rows($result) > 0) {
            $error_msg = "Error: Student with ID $st_id already exists!";
        } else {
            // Insert student details
            $insert_query = "INSERT INTO students (st_id, st_name, st_dept, st_batch, st_sem, st_email) VALUES ('$st_id', '$st_name', '$st_dept', '$st_batch', '$st_sem', '$st_email')";

            if (mysqli_query($conn, $insert_query)) {
                $success_msg = "Student added successfully!";
            } else {
                $error_msg = "Error adding student: " . mysqli_error($conn);
            }
        }
    }

    if (isset($_POST['tcr'])) { // Save Teacher
        $tc_id = $_POST['tc_id'];
        $tc_name = $_POST['tc_name'];
        $tc_dept = $_POST['tc_dept'];
        $tc_email = $_POST['tc_email'];
        $tc_course = $_POST['tc_course'];

        // Check if teacher ID already exists
        $check_query = "SELECT * FROM teachers WHERE tc_id = '$tc_id'";
        $result = mysqli_query($conn, $check_query);

        if (mysqli_num_rows($result) > 0) {
            $error_msg = "Error: Teacher with ID $tc_id already exists!";
        } else {
            // Insert teacher details
            $insert_query = "INSERT INTO teachers (tc_id, tc_name, tc_dept, tc_email, tc_course) VALUES ('$tc_id', '$tc_name', '$tc_dept', '$tc_email', '$tc_course')";

            if (mysqli_query($conn, $insert_query)) {
                $success_msg = "Teacher added successfully!";
            } else {
                $error_msg = "Error adding teacher: " . mysqli_error($conn);
            }
        }
    }
}
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
<style>
  .message { padding: 10px; font-size: 15px; font-weight: bold; color: black; }
  .form-container { display: none; }
  .toggle-buttons { margin-bottom: 20px; }
</style>
<script>
$(document).ready(function() {
    $("#showStudentForm").click(function() {
        $("#studentForm").show();
        $("#teacherForm").hide();
    });
    $("#showTeacherForm").click(function() {
        $("#teacherForm").show();
        $("#studentForm").hide();
    });
});
</script>
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

<center>
<div class="message">
    <?php if($success_msg) echo "<span style='color:green;'>$success_msg</span>"; ?>
    <?php if($error_msg) echo "<span style='color:red;'>$error_msg</span>"; ?>
</div>

<div class="content">
    <div class="toggle-buttons">
        <button id="showStudentForm" class="btn btn-primary">Add Student</button>
        <button id="showTeacherForm" class="btn btn-success">Add Teacher</button>
    </div>

    <!-- Student Form -->
    <div class="form-container" id="studentForm">
        <form method="post" class="form-horizontal col-md-6 col-md-offset-3">
            <h4>Add Student's Information</h4>
            <input type="text" name="st_id" class="form-control" placeholder="Reg. No." required />
            <input type="text" name="st_name" class="form-control" placeholder="Full Name" required />
            <input type="text" name="st_dept" class="form-control" placeholder="Department" required />
            <input type="text" name="st_batch" class="form-control" placeholder="Class ID" required />
            <input type="text" name="st_sem" class="form-control" placeholder="Semester" required />
            <input type="email" name="st_email" class="form-control" placeholder="Valid Email" required />
            <input type="submit" class="btn btn-primary" value="Add Student" name="std" />
        </form>
    </div>

    <!-- Teacher Form -->
    <div class="form-container" id="teacherForm">
        <form method="post" class="form-horizontal col-md-6 col-md-offset-3">
            <h4>Add Teacher's Information</h4>
            <input type="text" name="tc_id" class="form-control" placeholder="Teacher ID" required />
            <input type="text" name="tc_name" class="form-control" placeholder="Full Name" required />
            <input type="text" name="tc_dept" class="form-control" placeholder="Department" required />
            <input type="email" name="tc_email" class="form-control" placeholder="Valid Email" required />
            <input type="text" name="tc_course" class="form-control" placeholder="Subject Name" required />
            <input type="submit" class="btn btn-success" value="Add Teacher" name="tcr" />
        </form>
    </div>
</div>
</center>
</body>
</html>
