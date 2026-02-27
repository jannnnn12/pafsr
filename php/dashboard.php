<?php
session_start();
if (!isset($_SESSION['user_id'])) {  
    header("Location: login.php");
    exit();
}
include "db_connect.php";

$sql = "SELECT COUNT(*) as total_students FROM students";
$result = $conn->query($sql);

if ($result && $row = $result->fetch_assoc()) {
    $total_students = $row['total_students'];
} else {
    $total_students = 0; 
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Dashboard | RetentionX</title>
    <link rel="stylesheet" href="../css/dashboard.css"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&family=Open+Sans:wght@300;400;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
        <a href="profile.php"><i class="fas fa-user"></i>My Profile</a>
        <a href="#"><i class="fas fa-cog"></i>Account Settings</a>
        <a href="#"><i class="fas fa-edit"></i>Edit Profile</a>
    
    </div>
</div>
        </div>
        <ul class="sidebar-nav">
            <li>
                <a href="#" class="active">
                    <img src="../img/startup.png" alt="Rocket icon"/>
                    Dashboard
                </a>
            </li>
            <li><a href="viewstudent.php"> <img src="../img/checklist.png" alt="Rocket icon"/>Student List</a></li>
            <li><a href="student_data.php"> <img src="../img/student-profile.png" alt="List"/>Data Management</a></li>
            <li><a href="interventions.php"> <img src="../img/good-business.png" alt="Recommend"/>Interventions</a></li>
            <li><a href="alert.php"> <img src="../img/warning.png" alt="reports"/>Notification and Alerts</a></li>
            <li><a href="reports.php"> <img src="../img/file.png" alt="reports"/>Reports &amp; History</a></li>
            <li><a href="settings.php"> <img src="../img/cogwheel.png" alt="setting"/>Settings</a></li>
        </ul>
        <button class="logout-btn" id="logoutBtn">Log Out</button>
        <script>
            document.getElementById("logoutBtn").addEventListener("click", function() {
                let confirmLogout = confirm("Are you sure you want to log out?");
                if (confirmLogout) {
                    window.location.href = "login.php"; // Redirect to login page
                }
            });
        </script>
    </div>

    <div class="mobile-menu-btn">
        <button id="menuButton">
            <i class="fas fa-bars"></i>
        </button>
    </div>

    <div class="main-content">
        <div class="search-filter">
            <div class="search-container">
                <h3>Search &amp; Filter Options</h3>
                <input type="text" placeholder="Search students, grades, or reports..."/>
                <button class="search-btn">Search</button>
            </div>
        </div>

        <h2>Overview</h2>

        <div class="overview-grid">
            <div class="overview-card">
                <i class="fas fa-users text-blue-500"></i>
                <div class="overview-card-content">
                    <h3>Total Students Managed</h3>
                    <p><?php echo $total_students; ?></p>
                </div>
            </div>
            <div class="overview-card">
                <i class="fas fa-exclamation-triangle text-red-500"></i>
                <div class="overview-card-content">
                    <h3>At-Risk Students</h3>
                    <p>15</p>
                </div>
            </div>
            <div class="overview-card">
                <i class="fas fa-chart-line text-green-500"></i>
                <div class="overview-card-content">
                    <h3>Average Performance Score</h3>
                    <p>85%</p>
                </div>
            </div>
            <div class="overview-card">
                <i class="fas fa-calendar-check text-yellow-500"></i>
                <div class="overview-card-content">
                    <h3>Attendance Overview</h3>
                    <p>92% Present</p>
                </div>
            </div>
        </div>

        <div class="charts-grid">
            <div class="chart-container">
                <h3>Performance Trends</h3>
                <canvas id="performanceTrendsChart"></canvas>
            </div>
            <div class="chart-container">
                <h3>Retention Prediction</h3>
                <canvas id="retentionPredictionChart"></canvas>
            </div>
            <div class="chart-container">
                <h3>Intervention Effectiveness</h3>
                <canvas id="interventionEffectivenessChart"></canvas>
            </div>
        </div>

        <div class="interventions-section">
            <h3>Interventions &amp; Recommendations</h3>
            <p>AI-generated suggestions on improving student retention:</p>
            <ul>
                <li>
                    <i class="fas fa-lightbulb text-green-500"></i>
                    Schedule one-on-one meetings with at-risk students.
                </li>
                <li>
                    <i class="fas fa-lightbulb text-green-500"></i>
                    Implement peer tutoring sessions.
                </li>
                <li>
                    <i class="fas fa-lightbulb text-green-500"></i>
                    Provide additional resources for struggling students.
                </li>
            </ul>
            <p>Students who need attention with suggested actions:</p>
            <ul>
                <li>
                    <i class="fas fa-user text-blue-500"></i>
                    Jane Smith - Schedule a meeting
                </li>
                <li>
                    <i class="fas fa-user text-blue-500"></i>
                    John Doe - Peer tutoring
                </li>
                <li>
                    <i class="fas fa-user text-blue-500"></i>
                    Mary Johnson - Additional resources
                </li>
            </ul>
        </div>
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
        const performanceTrendsChart = document.getElementById('performanceTrendsChart').getContext('2d');
        new Chart(performanceTrendsChart, {
            type: 'line',
            data: {
                labels: ['January', 'February', 'March', 'April', 'May', 'June'],
                datasets: [{
                    label: 'Performance Score',
                    data: [80, 85, 90, 87, 92, 95],
                    borderColor: 'rgba(75, 192, 192, 1)',
                    fill: false
                }]
            }
        });
        
        const retentionPredictionChart = document.getElementById('retentionPredictionChart').getContext('2d');
        new Chart(retentionPredictionChart, {
            type: 'bar',
            data: {
                labels: ['Class 1', 'Class 2', 'Class 3', 'Class 4'],
                datasets: [{
                    label: 'Retention Prediction',
                    data: [95, 88, 92, 80],
                    backgroundColor: 'rgba(153, 102, 255, 0.2)',
                    borderColor: 'rgba(153, 102, 255, 1)',
                    borderWidth: 1
                }]
            }
        });
        
        const interventionEffectivenessChart = document.getElementById('interventionEffectivenessChart').getContext('2d');
        new Chart(interventionEffectivenessChart, {
            type: 'pie',
            data: {
                labels: ['Effective', 'Needs Improvement', 'Ineffective'],
                datasets: [{
                    data: [60, 30, 10],
                    backgroundColor: ['#36A2EB', '#FFCE56', '#FF5733']
                }]
            }
        });
    </script>
</body>
</html>
