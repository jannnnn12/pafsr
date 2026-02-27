<?php
session_start();
$servername = "localhost"; // Change if necessary
$username = "root"; // Change if necessary
$password = ""; // Change if necessary
$dbname = "pafsr_system"; // Change to your actual database name

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    if ($new_password !== $confirm_password) {
        echo "<script>alert('New passwords do not match.');</script>";
    } else {
        $sql = "SELECT password FROM user_details WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($hashed_password);
            $stmt->fetch();
            
            if (password_verify($current_password, $hashed_password)) {
                $new_hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
                $update_sql = "UPDATE user_details SET password = ? WHERE username = ?";
                $update_stmt = $conn->prepare($update_sql);
                $update_stmt->bind_param("ss", $new_hashed_password, $username);
                
                if ($update_stmt->execute()) {
                    echo "<script>alert('Password updated successfully.'); window.location.href = 'login.php';</script>";
                    exit();
                } else {
                    echo "<script>alert('Error updating password.');</script>"; 
                }
            } else {
                echo "<script>alert('Current password is incorrect.');</script>";
            }
        } else {
            echo "<script>alert('User not found.');</script>";
        }
        
        $stmt->close();
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password | RetentionX</title>
    <style>
        body {
            background-color: #4A5568; /* Gray-700 */
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
        }
        .container {
            background-color: white;
            padding: 32px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }
        h1 {
            font-size: 24px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 16px;
        }
        h2 {
            font-size: 20px;
            font-weight: 600;
            color: #1E3A8A; /* Blue-900 */
            margin-bottom: 8px;
        }
        p {
            color: #4A5568; /* Gray-600 */
            margin-bottom: 24px;
        }
        label {
            display: block;
            color: #4A5568; /* Gray-700 */
            margin-bottom: 8px;
        }
        input {
            width: 100%;
            padding: 12px;
            border: 1px solid #CBD5E0; /* Gray-300 */
            border-radius: 4px;
            margin-bottom: 16px;
            outline: none;
            transition: border-color 0.2s;
        }
        input:focus {
            border-color: #3B82F6; /* Blue-500 */
        }
        button {
            width: 100%;
            background-color: #1E3A8A; /* Blue-900 */
            color: white;
            padding: 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        button:hover {
            background-color: #1E40AF; /* Blue-800 */
        }
        .link {
            text-align: center;
            margin-top: 16px;
        }
        .link a {
            color: #3B82F6; /* Blue-500 */
            text-decoration: none;
        }
        .link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>RETENTION X</h1>
        <h2>New Password</h2>
        <p>Set the new password for your account so you can login and access all features.</p>
        <form method="POST">
    <label for="username">Enter username</label>
    <input type="text" id="username" placeholder="Username" name="username" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" required>
    
    <label for="current-password">Enter current password</label>
    <input type="password" id="current-password" placeholder="Current password" name="current_password" value="<?php echo isset($_POST['current_password']) ? htmlspecialchars($_POST['current_password']) : ''; ?>" required>
    
    <label for="new-password">Enter new password</label>
    <input type="password" id="new-password" placeholder="8 symbols at least" name="new_password" required>
    
    <label for="confirm-password">Confirm password</label>
    <input type="password" id="confirm-password" placeholder="8 symbols at least" name="confirm_password" required> 
    
    <button type="submit">UPDATE PASSWORD</button>
</form>
        <div class="link">
            <a href="login.php">Back to Sign In</a>
        </div>
    </div>
</body>
</html>