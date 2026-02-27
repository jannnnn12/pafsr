<?php
session_start();
include "db_connect.php"; // Ensure this file correctly connects to your database

$username_error = $password_error = ""; // Initialize error messages

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username_or_email = trim($_POST["username"]);
    $password = trim($_POST["password"]);

    if (empty($username_or_email)) {
        $username_error = "Username or Email is required!";
    }
    if (empty($password)) {
        $password_error = "Password is required!";
    }

    if (empty($username_error) && empty($password_error)) {
        // Query to check user credentials
        $stmt = $conn->prepare("SELECT id, username, email, password, role, verified FROM user_details WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username_or_email, $username_or_email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            // Ensure the account is verified
            if ($user['verified'] !== 'verified') {
                echo "<script>alert('Your account is pending or rejected. Please contact support.'); window.location.href = 'login.php';</script>";
                exit();
            }

            // Secure password verification
            if (password_verify($password, $user['password'])) {
                // Store session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];

                // Redirect based on role
                if ($user["role"] === "admin") {
                    header("Location: admin_viewstudent.php"); // Redirect admin users
                } else {
                    header("Location: dashboard.php"); // Redirect normal users
                }
                exit();
            } else {
                $password_error = "Invalid password!";
            }
        } else {
            $username_error = "User not found!";
        }
        $stmt->close();
    }
}
?>