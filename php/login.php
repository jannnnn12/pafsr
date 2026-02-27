<?php
session_start();
include 'db_connect.php'; // Ensure database connection

$username_error = $password_error = ""; // Initialize error messages

// Hardcoded admin credentials
$admin_username = "admin";
$admin_email = "admin@gmail.com";
$admin_password = "12341234"; // Not hashed for simple comparison

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username_or_email = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username_or_email)) {
        $username_error = "Username or Email is required!";
    }
    if (empty($password)) {
        $password_error = "Password is required!";
    }

    if (empty($username_error) && empty($password_error)) {
        // Check if the user is the admin
        if (($username_or_email === $admin_username || $username_or_email === $admin_email) && $password === $admin_password) {
            $_SESSION['user_id'] = "1"; // Static ID for admin
            $_SESSION['username'] = $admin_username;
            $_SESSION['role'] = "admin";
            header("Location: admin_viewteacher.php");
            exit();
        }

        // Check in the database for regular users
        $stmt = $conn->prepare("SELECT id, username, email, password, verified FROM user_details WHERE (username = ? OR email = ?)");
        $stmt->bind_param("ss", $username_or_email, $username_or_email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            
            // Check if the account is approved
            if ($user['verified'] !== 'verified') {
                $username_error = "Your account is still pending or has been rejected!";
            } 
            elseif (password_verify($password, $user['password'])) { // Secure password check
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = "user"; 
                header("Location: dashboard.php");
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

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
  <title>Login | RetentionX</title>
  <link rel="stylesheet" href="../css/login.css"/>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&family=Open+Sans:wght@300;400;700&display=swap" rel="stylesheet">
  <link rel="icon" type="image/x-icon" href="../img/logo1.png">
  
</head>
<body>
  <div class="container">
    <div class="left-section">
      <div class="logo">
        <img alt="Analytics Retention X logo" height="120" src="../img/logosystem.jpg" width="300"/>
      </div>
      <h1>Login</h1>
      <p>Enter your account details</p>

      <form method="POST">
        <div class="form-group">
          <label for="username">Username or Email</label>
          <input id="username" name="username" type="text" placeholder="Enter Your Username" value="<?php echo isset($username_or_email) ? htmlspecialchars($username_or_email) : ''; ?>" required/>
          <?php if (!empty($username_error)) echo "<p class='error-message'style='margin-top:10px'>$username_error</p>"; ?>
        </div>
        <div class="form-group password-container">
          <label for="password">Password</label>
          <input id="password" name="password" type="password" placeholder="Enter Your Password" required/>
          <i class="fas fa-eye eye-icon" onclick="togglePassword()"></i>
          <?php if (!empty($password_error)) echo "<p class='error-message' style='margin-top:20px'>$password_error</p>"; ?>
        </div>
        <div class="forgot-password">
          <a href="forgot_password.php" class="text-sm text-blue-500">Forgot Password?</a>
        </div>
        <div class="terms">
          <input id="terms" name="terms" type="checkbox" required/>
          <label for="terms">Agree with <a class="text-blue-500" href="#">terms &amp; conditions</a></label>
        </div>
        <button type="submit">Log In</button>
        <button type="button" onclick="window.location.href='../index.html'" class="but1">
  Go Back
</button>
      </form>

      <div class="signup">
        <p>Don't have an account? <a class="text-blue-500" href="signup.php">Sign up</a></p>
      </div>
      </div>

    <div class="right-section">
      <img alt="Golden High School building" src="../img/bg.jpg"/>
    </div>
  </div>

  <script>
    function togglePassword() {
        var passwordField = document.getElementById("password");
        var eyeIcon = document.querySelector(".eye-icon");

        if (passwordField.type === "password") {
            passwordField.type = "text";
            eyeIcon.classList.add("fa-eye-slash");
        } else {
            passwordField.type = "password";
            eyeIcon.classList.remove("fa-eye-slash");
        }
    }
  </script>
  <script>
  setTimeout(() => {
    document.querySelector("h1").classList.add("finished");
  }, 4000); // Removes border after 4 seconds
</script>
</body>
</html>
