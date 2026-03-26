<?php
ob_start();
session_start();

if ($_SESSION['name'] != 'oasis') {
    header('location: ../index.php');
}

// Establish database connection
include('connect.php');

// Fetch teacher details
if (isset($_GET['id'])) {
    $teacher_id = $_GET['id'];
    $query = mysqli_query($conn, "SELECT * FROM teachers WHERE tc_id='$teacher_id'");
    $teacher = mysqli_fetch_assoc($query);

    if (!$teacher) {
        die("Teacher not found.");
    }
} else {
    header("Location: v-teachers.php");
    exit();
}

// Update teacher details
if (isset($_POST['update'])) {
    $name = $_POST['name'];
    $dept = $_POST['dept'];
    $email = $_POST['email'];
    $course = $_POST['course'];

    $update_query = "UPDATE teachers SET tc_name='$name', tc_dept='$dept', tc_email='$email', tc_course='$course' WHERE tc_id='$teacher_id'";

    if (mysqli_query($conn, $update_query)) {
        header("Location: v-teachers.php");
        exit();
    } else {
        echo "Error updating record: " . mysqli_error($conn);
    } 
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit Teacher</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
</head>

<body>
   

    <div class="container">
        <h2 class="text-center">Edit Teacher Details</h2>
        <form method="POST">
            <div class="form-group">
                <label>Teacher Name:</label>
                <input type="text" name="name" class="form-control" value="<?php echo $teacher['tc_name']; ?>" required>
            </div>
            <div class="form-group">
                <label>Department:</label>
                <input type="text" name="dept" class="form-control" value="<?php echo $teacher['tc_dept']; ?>" required>
            </div>
            <div class="form-group">
                <label>Email:</label>
                <input type="email" name="email" class="form-control" value="<?php echo $teacher['tc_email']; ?>" required>
            </div>
            <div class="form-group">
                <label>Course:</label>
                <input type="text" name="course" class="form-control" value="<?php echo $teacher['tc_course']; ?>" required>
            </div>
            <button type="submit" name="update" class="btn btn-success">Update</button>
            <a href="v-teachers.php" class="btn btn-danger">Cancel</a>
        </form>
    </div>
</body>
</html>
