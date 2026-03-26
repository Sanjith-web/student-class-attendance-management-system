<?php
include('connect.php');

if (isset($_POST['batch'])) {
    $batch = $_POST['batch'];
    $query = "SELECT * FROM students";
    if (!empty($batch)) {
        $query .= " WHERE st_batch = '$batch'";
    }

    $result = mysqli_query($conn, $query);
    $serial = 1; // Initialize serial number

    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>
                <td>{$serial}</td>
                <td>{$row['st_id']}</td>
                <td>{$row['st_name']}</td>
                <td>{$row['st_dept']}</td>
                <td>{$row['st_batch']}</td>
                <td>{$row['st_sem']}</td>
                <td>{$row['st_email']}</td>
              </tr>";
        $serial++; // Increment serial number
    }
}
?>
