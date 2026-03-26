<?php
ob_start();
session_start();

if ($_SESSION['name'] != 'oasis') {
    header('location: ../index.php');
}

include('connect.php');

// Check if the edit ID is provided
if (isset($_GET['edit_id'])) {
    $edit_id = mysqli_real_escape_string($conn, $_GET['edit_id']);
    $student_query = mysqli_query($conn, "SELECT * FROM students WHERE st_id = '$edit_id'");
    $student_data = mysqli_fetch_assoc($student_query);
    
    if (!$student_data) {
        echo "Student not found.";
        exit();
    }
} else {
    echo "No student selected.";
    exit();
}

// Handle form submission for editing
if (isset($_POST['update'])) {
    $st_name = mysqli_real_escape_string($conn, $_POST['st_name']);
    $st_dept = mysqli_real_escape_string($conn, $_POST['st_dept']);
    $st_batch = mysqli_real_escape_string($conn, $_POST['st_batch']);
    $st_sem = mysqli_real_escape_string($conn, $_POST['st_sem']);
    $st_email = mysqli_real_escape_string($conn, $_POST['st_email']);

    // Update the student's details in the database
    $update_query = "UPDATE students SET st_name = '$st_name', st_dept = '$st_dept', 
                     st_batch = '$st_batch', st_sem = '$st_sem', st_email = '$st_email' 
                     WHERE st_id = '$edit_id'";

    if (mysqli_query($conn, $update_query)) {
        header("Location: v-students.php"); // Redirect to the students list after update
    } else {
        echo "Error updating student details.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit Student</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="../css/main.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
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

<div class="container">
    <h2>Edit Student Details</h2>
    <form method="POST">
        <div class="form-group">
            <label for="st_name">Name</label>
            <input type="text" name="st_name" class="form-control" value="<?php echo $student_data['st_name']; ?>" required>
        </div>
        <div class="form-group">
            <label for="st_dept">Department</label>
            <input type="text" name="st_dept" class="form-control" value="<?php echo $student_data['st_dept']; ?>" required>
        </div>
        <div class="form-group">
            <label for="st_batch">Class ID</label>
            <input type="text" name="st_batch" class="form-control" value="<?php echo $student_data['st_batch']; ?>" required>
        </div>
        <div class="form-group">
            <label for="st_sem">Semester</label>
            <input type="text" name="st_sem" class="form-control" value="<?php echo $student_data['st_sem']; ?>" required>
        </div>
        <div class="form-group">
            <label for="st_email">Email</label>
            <input type="email" name="st_email" class="form-control" value="<?php echo $student_data['st_email']; ?>" required>
        </div>
        <button type="submit" name="update" class="btn btn-success">Update</button>
        <a href="v-students.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>

</body>
</html>
