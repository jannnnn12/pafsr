<?php
//viewstudent.php
session_start();
if (!isset($_SESSION['user_id'])) {  
    header("Location: login.php");
    exit();
}
include "db_connect.php";

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student List | RetentionX</title>
    <link rel="stylesheet" href="../css/viewstudent.css"/>
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
            <li><a href="#" class="active"> <img src="../img/checklist.png" alt="Rocket icon"/>Student List</a></li>
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
    <main class="main-content">
        <header>
            <h1>Students</h1>
            <button class="add-button" onclick="goToAddStudent()">Add New</button>
            <script>
            function goToAddStudent() {
                window.location.href = "add_student.php"; 
            }
    </script>
        </header>
        <section class="student-list">
            <h2>All Students List</h2>
           
            <div class="search-bar">
            <input type="text" id="searchInput" placeholder="Search by Name or Status...">
            <button id="searchBtn">Enter</button>
        </div>
            <table id="studentTable"> 
               
                <thead>
                    <tr>
                        <th>Student ID</th>
                        <th>Full Name</th>
                        <th>Year Level</th>
                        <th>Course / Program</th>       
                        <th>Enrollment Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $query = "SELECT student_id, CONCAT(first_name, ' ', last_name) AS full_name, year_level, course, status FROM students";
                $result = mysqli_query($conn, $query);

                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>
                                <td>{$row['student_id']}</td>
                                <td class='name'>{$row['full_name']}</td>
                                <td>{$row['year_level']}</td>
                                <td>{$row['course']}</td>
                                <td>{$row['status']}</td>

                               <td class='action-buttons'>
                                <a class='view' href='action_view.php?student_id={$row['student_id']}'>View</a>
                                <button class='delete' onclick='deleteStudent({$row['student_id']})'>Delete</button>
                            </td>

                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='6'>No students found.</td></tr>";
                }
                
                ?>
                </tbody>
            </table>
        </section>
    </main>
    <script>

        function viewStudent(studentId) {
            window.location.href = `view_student.php?id=${studentId}`;
        }

        function editStudent(studentId) {
            window.location.href = `edit_student.php?id=${studentId}`;
        }

        function deleteStudent(studentId) {
            // Open a confirmation dialog
            if (confirm("Are you sure you want to delete this student?")) {
                // If confirmed, redirect to the delete_student.php file with the student ID as a query parameter
                window.location.href = `delete_student.php?id=${studentId}`;
            }
        }
        
        window.addEventListener("pageshow", function (event) {
        if (event.persisted) {
            window.location.reload();
        }
    });

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

        document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.getElementById("searchInput");
    const searchBtn = document.getElementById("searchBtn");

    // Trigger search when the Enter button is clicked
    searchBtn.addEventListener("click", function () {
        performSearch();
    });

    // Trigger search when Enter key is pressed inside the input field
    searchInput.addEventListener("keypress", function (event) {
        if (event.key === "Enter") {
            event.preventDefault(); // Prevent form submission
            performSearch();
        }
    });

    function performSearch() {
        let filter = searchInput.value.toLowerCase();
        let table = document.getElementById("studentTable");
        let rows = table.getElementsByTagName("tr");

        for (let i = 1; i < rows.length; i++) {
            let name = rows[i].getElementsByClassName("name")[0]?.textContent.toLowerCase() || "";
            let status = rows[i].getElementsByTagName("td")[4]?.textContent.toLowerCase() || "";
            
            if (name.includes(filter) || status.includes(filter)) {
                rows[i].style.display = "";
            } else {
                rows[i].style.display = "none";
            }
        }
    }
});
        </script>
</body>
</html>
