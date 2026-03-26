<?php
ob_start();
session_start();

if ($_SESSION['name'] != 'oasis') {
    header('location: ../index.php');
}

include('connect.php');

// Handle delete request
if (isset($_GET['delete_id'])) {
    $delete_id = mysqli_real_escape_string($conn, $_GET['delete_id']);
    mysqli_query($conn, "DELETE FROM students WHERE st_id = '$delete_id'");
    header("Location: v-students.php"); // Refresh the page after deletion
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
<h1>All Students</h1>
<div class="content">
    <form method="POST" class="form-inline">
        <label for="class_id">Select Class ID:</label>
        <select name="class_id" class="form-control" required>
            <option value="">Select</option>
            <?php
            $class_query = mysqli_query($conn, "SELECT DISTINCT st_batch FROM students");
            while ($class_row = mysqli_fetch_assoc($class_query)) {
                echo "<option value='{$class_row['st_batch']}'>{$class_row['st_batch']}</option>";
            }
            ?>
        </select>
        <button type="submit" name="filter" class="btn btn-primary">Filter</button>
    </form>
    <br>
    <table class="table table-striped table-hover">
    <thead>
        <tr>
            <th>S.No.</th> <!-- New Column -->
            <th>Registration No.</th>
            <th>Name</th>
            <th>Department</th>
            <th>Class ID</th>
            <th>Semester</th>
            <th>Email</th>
            <th>Action</th> <!-- Column for Delete Option -->
        </tr>
    </thead>
    <tbody>
        <?php
        $query = "SELECT * FROM students ORDER BY st_id ASC";
        if (isset($_POST['filter']) && !empty($_POST['class_id'])) {
            $class_id = mysqli_real_escape_string($conn, $_POST['class_id']);
            $query = "SELECT * FROM students WHERE st_batch = '$class_id' ORDER BY st_id ASC";
        }
        $all_query = mysqli_query($conn, $query);
        $serial = 1; // Initialize Serial Number Counter
        while ($data = mysqli_fetch_array($all_query)) { ?>
            <tr>
                <td><?php echo $serial++; ?></td> <!-- Serial Number -->
                <td><?php echo $data['st_id']; ?></td>
                <td><?php echo $data['st_name']; ?></td>
                <td><?php echo $data['st_dept']; ?></td>
                <td><?php echo $data['st_batch']; ?></td>
                <td><?php echo $data['st_sem']; ?></td>
                <td><?php echo $data['st_email']; ?></td>
                            <td>
    <a href="edit-student.php?edit_id=<?php echo $data['st_id']; ?>" 
       class="btn btn-primary btn-sm">
       Edit
    </a>
    <a href="v-students.php?delete_id=<?php echo $data['st_id']; ?>" 
       class="btn btn-danger btn-sm" 
       onclick="return confirm('Are you sure you want to delete this student?');">
       Delete
    </a>
</td>

            </tr>
        <?php } ?>
    </tbody>
</table>

</div>

</center>
<center>
   
        <!-- Modify the Download Button to include the class_id -->
        <a href="download-students.php?class_id=<?php echo isset($_POST['class_id']) ? $_POST['class_id'] : ''; ?>" class="btn btn-success">
            Download Student List
        </a>
        <br><br>
        <table class="table table-striped table-hover">
            <!-- Table content goes here -->
        </table>
    </div>
</center>


</body>
</html>
