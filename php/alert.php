<?php
session_start();
if (!isset($_SESSION['user_id'])) {  
    header("Location: login.php");
    exit();
}
include "db_connect.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Alert | RetentionX</title>
    <link rel="stylesheet" href="../css/alert.css"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&family=Open+Sans:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="../img/logo1.png">
    
 </head>
<body>
<div class="sidebar" id="sidebar">
        <h1>RetentionX</h1>
        <div class="user-profile">
            <div class="user-profile-button" id="userProfileButton">
                <img src="../img/user.png" alt="User profile picture"/>
                <span class="user-profile-name"><?php echo htmlspecialchars($_SESSION['username'] ?? "Teacher"); ?></span>
                <i class="fas fa-chevron-down"></i>
            </div>
            <div class="user-profile-dropdown" id="userProfileDropdown">
    <div class="dropdown-header">
        <span class="close-dropdown" id="closeDropdown">&times;</span>
        <img src="../img/user.png" alt="User profile"/>
        <div class="user-name"><?php echo htmlspecialchars($_SESSION['username'] ?? "Teacher"); ?></div>
        <div class="user-email"><?php echo htmlspecialchars($_SESSION['email'] ?? "No Email Available"); ?></div>
    </div>
    <div class="dropdown-menu">
        <a href="#"><i class="fas fa-user"></i>My Profile</a>
        <a href="#"><i class="fas fa-cog"></i>Account Settings</a>
        <a href="#"><i class="fas fa-edit"></i>Edit Profile</a>
    
    </div>
</div>
        </div>
        <ul class="sidebar-nav">
            <li>
                <a href="dashboard.php">
                    <img src="../img/startup.png" alt="Rocket icon"/>
                    Dashboard
                </a>
            </li>
            <li><a href="viewstudent.php"> <img src="../img/checklist.png" alt="Rocket icon"/>Student List</a></li>
            <li><a href="student_data.php"> <img src="../img/student-profile.png" alt="List"/>Data Management</a></li>
            <li><a href="interventions.php"> <img src="../img/good-business.png" alt="Recommend"/>Interventions</a></li>
            <li><a href="#" class="active"> <img src="../img/warning.png" alt="reports"/>Notification and Alerts</a></li>
            <li><a href="reports.php"> <img src="../img/file.png" alt="reports"/>Reports &amp; History</a></li>
            <li><a href="settings.php"> <img src="../img/cogwheel.png" alt="setting"/>Settings</a></li>
        </ul>
        <button class="logout-btn" id="logoutBtn">Log Out</button>
        <script>
            document.getElementById("logoutBtn").addEventListener("click", function() {
                let confirmLogout = confirm("Are you sure you want to log out?");
                if (confirmLogout) {
                    window.location.href = "login.php"; 
                }
            });
        </script>
    </div>
    <div class="mobile-menu-btn">
        <button id="menuButton">
            <i class="fas fa-bars"></i>
        </button>
    </div>
    <script>
               document.addEventListener("DOMContentLoaded", function () {
            const userProfileButton = document.getElementById("userProfileButton");
            const userProfileDropdown = document.getElementById("userProfileDropdown");
            const closeDropdown = document.getElementById("closeDropdown");

            // mo drop down inig click
            userProfileButton.addEventListener("click", function () {
                userProfileDropdown.classList.toggle("show");
            });

            // mo close
            closeDropdown.addEventListener("click", function () {
                userProfileDropdown.classList.remove("show");
            });

            // mo pislit sa gawas mo close
            document.addEventListener("click", function (event) {
                if (!userProfileButton.contains(event.target) && !userProfileDropdown.contains(event.target)) {
                    userProfileDropdown.classList.remove("show");
                }
            });
        });
        document.getElementById('menuButton').addEventListener('click', function() {
            var sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('show');
        });
        </script>
</body>
</html>