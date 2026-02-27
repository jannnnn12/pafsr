<?php
session_start();
if (!isset($_SESSION['user_id'])) {  
    header("Location: login.php");
    exit();
}

include "db_connect.php"; // Ensure you have a valid database connection

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['record_type']) && isset($_POST['record_id'])) {
    $record_type = $_POST['record_type'];
    $record_id = intval($_POST['record_id']); // Ensure record_id is an integer

    // Check the type of record and perform the delete action accordingly
    if ($record_type == 'attendance') {
        $query = "DELETE FROM student_attendance WHERE id = ?";
    } elseif ($record_type == 'assessment') {
        $query = "DELETE FROM student_assessments WHERE id = ?";  // Updated table name
    } elseif ($record_type == 'participation') {
        $query = "DELETE FROM student_participation WHERE id = ?";
    } else {
        echo "Invalid record type!";
        exit();
    }

    // Prepare and execute the SQL query to delete the record
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("i", $record_id); // "i" indicates the variable is an integer
        if ($stmt->execute()) {
            echo "Record deleted successfully. <a href='student_data.php'>go back</a>";
        } else {
            echo "Error deleting record: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Error preparing statement: " . $conn->error;
    }
} else {
    echo "Invalid request.";
}

$conn->close();
?>
