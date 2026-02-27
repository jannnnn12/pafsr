<?php
// delete_student.php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include "db_connect.php";

// Check if student ID is provided
if (isset($_GET['id'])) {
    $student_id = $_GET['id'];

    // Prepare the delete query
    $query = "DELETE FROM students WHERE student_id = ?";
    if ($stmt = mysqli_prepare($conn, $query)) {
        mysqli_stmt_bind_param($stmt, "i", $student_id);
        if (mysqli_stmt_execute($stmt)) {
            // Redirect back to the student list after successful deletion
            header("Location: viewstudent.php");
            exit();
        } else {
            echo "Error deleting record: " . mysqli_error($conn);
        }
    }
}
?>
