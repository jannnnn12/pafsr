<?php
//add_student_process.php
session_start();
include "db_connect.php";

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get form values and sanitize input
$student_id = isset($_POST['student_id']) ? trim($_POST['student_id']) : '';
$first_name = trim($_POST['first_name']);
$last_name = trim($_POST['last_name']);
$email = trim($_POST['email']);
$course = trim($_POST['course']);
$year_level = trim($_POST['year_level']);
$section = trim($_POST['section']);
$status = trim($_POST['status']);

// Validate required fields
if (empty($student_id) || empty($first_name) || empty($last_name) || empty($email) || empty($course) || empty($year_level)) {
    die("Error: All required fields must be filled. <a href='add_student.php'>Go Back</a>");
}

// Check if Student ID already exists
$check_id = $conn->prepare("SELECT student_id FROM students WHERE student_id = ?");
$check_id->bind_param("s", $student_id);
$check_id->execute();
$check_id->store_result();

if ($check_id->num_rows > 0) {
    die("Error: Student ID already exists. <a href='add_student.php'>Go Back</a>");
}
$check_id->close();

// Check if email already exists
$check_email = $conn->prepare("SELECT email FROM students WHERE email = ?");
$check_email->bind_param("s", $email);
$check_email->execute();
$check_email->store_result();

if ($check_email->num_rows > 0) {
    die("<script>alert('Error: Student ID already exists!'); window.location.href='add_student.php';</script>");
}
$check_email->close();

// Insert data into the database
$stmt = $conn->prepare("INSERT INTO students (student_id, first_name, last_name, email, course, year_level, section, status) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssssss", $student_id, $first_name, $last_name, $email, $course, $year_level, $section, $status);

if ($stmt->execute()) {
    echo "<script>alert('Student added successfully!'); window.location.href='viewstudent.php';</script>";
} else {
    echo "<script>alert('Error: Could not add student!'); window.location.href='add_student.php';</script>";
}

// Close connection
$stmt->close();
$conn->close();
?>
