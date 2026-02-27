<?php
// update_student.php
session_start();
include "db_connect.php";

header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['student_id']) || !isset($data['updates'])) {
    echo json_encode(["success" => false, "error" => "Invalid request"]);
    exit();
}

$student_id = intval($data['student_id']);
$updates = $data['updates'];

$allowedFields = [ 'first_name', 'last_name', 'email', 'course', 'year_level', 'section', 'status'];
$updateParts = [];
$params = [];
$paramTypes = "";

foreach ($updates as $column => $value) {
    if (in_array($column, $allowedFields)) {
        $updateParts[] = "$column = ?";
        $params[] = $value;
        $paramTypes .= ($column === 'student_id') ? "i" : "s";
    }
}

if (empty($updateParts)) {
    echo json_encode(["success" => false, "error" => "No valid fields to update"]);
    exit();
}

$query = "UPDATE students SET " . implode(", ", $updateParts) . " WHERE student_id = ?";
$params[] = $student_id;
$paramTypes .= "i";

$stmt = $conn->prepare($query);
$stmt->bind_param($paramTypes, ...$params);

if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "error" => $stmt->error]);
}

$stmt->close();
$conn->close();
?>
