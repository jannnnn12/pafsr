<?php
// verify_teacher.php
include "db_connect.php";
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["id"], $_POST["action"])) {
    $id = intval($_POST["id"]); // Ensure ID is an integer
    $action = $_POST["action"];

    if ($action === "verify") {
        $status = "verified";
        $message = "Verified successfully";
    } elseif ($action === "reject") {
        $status = "rejected";
        $message = "Rejected successfully";
    } else {
        echo json_encode(["success" => false, "message" => "Invalid action."]);
        exit;
    }

    // Update teacher's verification status in user_details
    $stmt = $conn->prepare("UPDATE user_details SET verified = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $id);

    if ($stmt->execute()) {
        // Log the action into teacher_verification_history
        $stmt_log = $conn->prepare("INSERT INTO teacher_verification_history (user_id, first_name, last_name, email, action)
                                     SELECT id, first_name, last_name, email, ? FROM user_details WHERE id = ?");
        $stmt_log->bind_param("si", $status, $id);
        if ($stmt_log->execute()) {
            echo json_encode(["success" => true, "message" => $message, "id" => $id, "status" => $status]);
        } else {
            echo json_encode(["success" => false, "message" => "Failed to record action in history: " . $stmt_log->error]);
        }
        $stmt_log->close();
    } else {
        echo json_encode(["success" => false, "message" => "Database error: " . $stmt->error]);
    }
    $stmt->close();
} else {
    echo json_encode(["success" => false, "message" => "Invalid request."]);
}

$conn->close();
?>