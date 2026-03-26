<?php
// Connect to the database
include('connect.php');

// Set headers for downloading the CSV file
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="students_list.csv"');

// Open the output stream
$output = fopen('php://output', 'w');

// Add column headers to the CSV
fputcsv($output, array('S.No.', 'Registration No.', 'Name', 'Department', 'Class ID', 'Semester', 'Email'));

// Fetch the student data from the database
$query = "SELECT * FROM students ORDER BY st_id ASC";
if (isset($_POST['filter']) && !empty($_POST['class_id'])) {
    $class_id = mysqli_real_escape_string($conn, $_POST['class_id']);
    $query = "SELECT * FROM students WHERE st_batch = '$class_id' ORDER BY st_id ASC";
}
$all_query = mysqli_query($conn, $query);

// Output the student data to the CSV
$serial = 1; // Serial number for rows
while ($data = mysqli_fetch_array($all_query)) {
    fputcsv($output, array(
        $serial++, 
        $data['st_id'], 
        $data['st_name'], 
        $data['st_dept'], 
        $data['st_batch'], 
        $data['st_sem'], 
        $data['st_email']
    ));
}

// Close the file pointer
fclose($output);
exit();
?>
