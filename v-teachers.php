<?php
ob_start();
session_start();

if ($_SESSION['name'] != 'oasis') {
    header('location: ../index.php');
}

// Establish database connection
include('connect.php');

// Handle delete request
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    mysqli_query($conn, "DELETE FROM teachers WHERE tc_id='$delete_id'");
    header("Location: v-teachers.php"); // Redirect after deletion
    exit();
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
        <h1>All Teachers</h1>
        <div class="content">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Teacher ID</th>
                        <th>Name</th>
                        <th>Department</th>
                        <th>Email</th>
                        <th>Course</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <?php
                $tcr_query = mysqli_query($conn, "SELECT * FROM teachers ORDER BY tc_id ASC");
                while ($tcr_data = mysqli_fetch_array($tcr_query)) {
                ?>
                    <tbody>
                        <tr>
                            <td><?php echo $tcr_data['tc_id']; ?></td>
                            <td><?php echo $tcr_data['tc_name']; ?></td>
                            <td><?php echo $tcr_data['tc_dept']; ?></td>
                            <td><?php echo $tcr_data['tc_email']; ?></td>
                            <td><?php echo $tcr_data['tc_course']; ?></td>
                            <td>
                                <a href="edit-teacher.php?id=<?php echo $tcr_data['tc_id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                <a href="v-teachers.php?delete_id=<?php echo $tcr_data['tc_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this teacher?');">Delete</a>
                            </td>
                        </tr>
                    </tbody>
                <?php } ?>
            </table>
        </div>
    </center>
</body>
</html>
