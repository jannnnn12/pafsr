<?php
//action_view.php
session_start();
if (!isset($_SESSION['user_id'])) {  
    header("Location: login.php");
    exit();
}
include "db_connect.php";

if (isset($_GET['student_id']) && is_numeric($_GET['student_id'])) {
    $student_id = $_GET['student_id'];

    $query = "SELECT * FROM students WHERE student_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $student = $result->fetch_assoc();
    } else {
        echo "<script>alert('Student not found!'); window.location.href='viewstudent.php';</script>";
        exit();
    }
} else {
    echo "<script>alert('Invalid student ID!'); window.location.href='viewstudent.php';</script>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    header('Content-Type: application/json');
    $column = $_POST['column'];
    $newValue = $_POST['value'];

    if (in_array($column, ['email', 'course', 'year_level', 'section', 'status'])) {
        $updateQuery = "UPDATE students SET $column = ? WHERE student_id = ?";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bind_param("si", $newValue, $student_id);
        
        if ($updateStmt->execute()) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "error" => $updateStmt->error]);
        }
        exit();
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Student List | RetentionX</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&family=Open+Sans:wght@300;400;700&display=swap" rel="stylesheet">
   
    <link rel="icon" type="image/x-icon" href="../img/logo1.png">
    <style>
         /* Reset and Base Styles */
         * {
            transition: all 0.3s ease;
        }

        /* Sidebar Animations */
        @keyframes slideIn {
            from {
                transform: translateX(-100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        .sidebar {
            animation: slideIn 0.5s ease forwards;
        }

        /* User Profile Dropdown Animations */
        @keyframes dropdownFade {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .user-profile-dropdown.show {
            animation: dropdownFade 0.3s ease forwards;
        }
        .logout-btn {
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

         .logout-btn::after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: rgba(255,255,255,0.2);
            transition: left 0.3s ease;
        }

        .logout-btn:hover::after {
            left: 0;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            background-color: #e5e7eb;
            display: flex;
        }

        /* Sidebar Styles */
        .sidebar {
            width: 250px;
            min-height: 100vh;
            background-color: #000;
            color: #fff;
            padding: 20px;
            display: none;
        }

        .sidebar.show {
            display: block;
        }

        .sidebar h1 {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 2rem;
            text-align: center;
        }
        
        /* User Profile Styles */
        .user-profile {
            position: relative;
            margin-bottom: 2rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .user-profile-button {
            display: flex;
            flex-direction: column;
            align-items: center;
            cursor: pointer;
            margin-bottom: 1rem;
        }

        .user-profile-button img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            margin-bottom: 0.5rem;
            border: 3px solid #3b82f6;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .user-profile-name {
            font-weight: bold;
            color: #fff;
            margin-bottom: 0.5rem;
            font-size: 1.1rem;
        }

        .user-profile-dropdown {
            position: absolute;
            bottom: -8rem;
            background-color: #fff;
            color: #000;
            border-radius: 0.75rem;
            box-shadow: 0 15px 25px rgba(0,0,0,0.15);
            width: 250px;
            display: none;
            z-index: 10;
            overflow: hidden;
            border: 1px solid #e5e7eb;
        }

        .user-profile-dropdown::before {
            content: '';
            position: absolute;
            top: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 0;
            height: 0;
            border-left: 10px solid transparent;
            border-right: 10px solid transparent;
            border-bottom: 10px solid #fff;
            z-index: 11;
        }

        .user-profile-dropdown.show {
            display: block;
            animation: dropdownSlideIn 0.3s ease forwards;
        }

        @keyframes dropdownSlideIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .user-profile-dropdown .dropdown-header {
            background-color: #f3f4f6;
            padding: 1rem;
            text-align: center;
            border-bottom: 1px solid #e5e7eb;
        }

        .user-profile-dropdown .dropdown-header img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            margin-bottom: 0.5rem;
            border: 2px solid #3b82f6;
        }

        .user-profile-dropdown .dropdown-header .user-email {
            color: #6b7280;
            font-size: 0.875rem;
        }

        .user-profile-dropdown .dropdown-menu {
            padding: 0.5rem 0;
        }

        .user-profile-dropdown a {
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            color: #374151;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }

        .user-profile-dropdown a:hover {
            background-color: #f3f4f6;
        }

        .user-profile-dropdown a i {
            margin-right: 0.75rem;
            color: #3b82f6;
            font-size: 1rem;
            width: 20px;
            text-align: center;
        }

        .user-profile-dropdown .dropdown-menu .logout-link {
            color: #dc2626;
            border-top: 1px solid #e5e7eb;
        }

        .user-profile-dropdown .dropdown-menu .logout-link:hover {
            background-color: #fee2e2;
        }

        .user-profile-dropdown .dropdown-menu .logout-link i {
            color: #dc2626;
        }
        .close-dropdown {
        position: absolute;
        top: 10px;
        right: 15px;
        font-size: 1.5rem;
        cursor: pointer;
        color: #374151;
        transition: color 0.3s ease;
        }

        .close-dropdown:hover {
        color: #dc2626;
        }

        /* Sidebar Navigation Styles */
        .sidebar-nav {
            list-style: none;
        }

        .sidebar-nav li {
            margin-bottom: 1rem;
        }

        .sidebar-nav a {
            display: flex;
            align-items: center;
            padding: 0.5rem;
            text-decoration: none;
            color: #fff;
        }

        .sidebar-nav a.active {
            background-color: #374151;
            border-radius: 0.25rem;
        }
        .sidebar-nav a:hover {
    background-color: #374151;
    border-radius: 0.25rem;
}
        .sidebar-nav a img {
            margin-right: 0.5rem;
            width: 20px;
            height: 20px;
        }

        .logout-btn {
            margin-top: 2rem;
            background-color: #dc2626;
            color: #fff;
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 0.25rem;
            cursor: pointer;
            width: 100%;
        }
        .mobile-menu-btn {
            display: block;
            padding: 1rem;
        }

        /* Main Content Section Styles */
.main-content {
    flex: 1;
    padding: 20px;
    background-color: #f9fafb;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: flex-start;
}

/* Header Styling */
.main-content header {
    width: 100%;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px;
    background-color: #ffffff;
    border-radius: 10px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    margin-bottom: 20px;
}

.main-content header h1 {
    font-size: 24px;
    font-weight: bold;
    color: #374151;
}

.main-content header button {
    padding: 10px 15px;
    background-color: #3b82f6;
    color: #ffffff;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.main-content header button:hover {
    background-color: #2563eb;
}

/* Styling for the Edit Form */
.student-details {
    width: 100%;
    max-width: 700px;
    background-color: #ffffff;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 5px 10px rgba(0, 0, 0, 0.15);
    transition: all 0.3s ease;
}

.student-details table {
    width: 100%;
    border-collapse: collapse;
}

.student-details th {
    text-align: left;
    padding: 15px;
    background-color: #f3f4f6;
    font-size: 16px;
    font-weight: 600;
    color: #374151;
    border-radius: 8px;
    width: 35%;
}

.student-details td {
    padding: 15px;
    font-size: 16px;
    border-bottom: 1px solid #e5e7eb;
}

.input-field {
    width: 100%;
    padding: 12px;
    font-size: 16px;
    border: 1px solid #ccc;
    border-radius: 8px;
    outline: none;
    transition: border 0.3s ease;
}

.input-field:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 5px rgba(59, 130, 246, 0.5);
}

/* Button Styling */
.button-group {
    display: flex;
    gap: 15px; /* More spacing between buttons */
}

.button-group button {
    padding: 12px 20px;
    font-size: 16px;
    font-weight: 600;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

#editButton {
    background-color: #3b82f6;
    color: white;
}

#editButton:hover {
    background-color: #2563eb;
}

#saveButton {
    background-color: #10b981;
    color: white;
}

#saveButton:hover {
    background-color: #059669;
}

/* Makes edit form appear larger when active */
.editing .student-details {
    transform: scale(1.02);
    box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
}

        @media (min-width: 1024px) {
            .sidebar {
                display: block;
                width: 250px;
            }
            .mobile-menu-btn {
                display: none;
            }
        }
        </style>
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
            <li><a href="viewstudent.php" class="active"> <img src="../img/checklist.png" alt="Rocket icon"/>Student List</a></li>
            <li><a href="student_data.php"> <img src="../img/student-profile.png" alt="List"/>Data Management</a></li>
            <li><a href="interventions.php"> <img src="../img/good-business.png" alt="Recommend"/>Interventions</a></li>
            <li><a href="alert.php"> <img src="../img/warning.png" alt="reports"/>Notification and Alerts</a></li>
            <li><a href="reports.php"> <img src="../img/file.png" alt="reports"/>Reports &amp; History</a></li>
            <li><a href="settings.php#"> <img src="../img/cogwheel.png" alt="setting"/>Settings</a></li>
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
    <h1>Student Details</h1>
    <div class="button-group">
        <button onclick="window.history.back();">Back</button>
        <button id="editButton">Edit</button>
        <button id="saveButton" style="display:none;">Save</button>
    </div>
</header>

    <section class="student-details">
        <table>
        <?php
$editableFields = ["first_name", "last_name", "email", "course", "year_level", "section", "status"];
foreach ($student as $key => $value) {
    echo "<tr><th>" . ucfirst(str_replace('_', ' ', $key)) . "</th><td>";
    
    if (in_array($key, $editableFields)) {
        if ($key == "status") {
            // Dropdown for status
            echo "<select class='input-field' data-column='$key' style='display:none;'>
                    <option value='Active' " . ($value == "Active" ? "selected" : "") . ">Active</option>
                    <option value='Dropped' " . ($value == "Dropped" ? "selected" : "") . ">Dropped</option>
                    <option value='Inactive' " . ($value == "Inactive" ? "selected" : "") . ">Inactive</option>
                  </select>";
            echo "<span class='text-field' data-column='$key' style='display:block;'>$value</span>";
        } elseif ($key == "year_level") {
            // Dropdown for year level
            echo "<select class='input-field' data-column='$key' style='display:none;'>
                    <option value='1st Year' " . ($value == "1st Year" ? "selected" : "") . ">1st Year</option>
                    <option value='2nd Year' " . ($value == "2nd Year" ? "selected" : "") . ">2nd Year</option>
                    <option value='3rd Year' " . ($value == "3rd Year" ? "selected" : "") . ">3rd Year</option>
                    <option value='4th Year' " . ($value == "4th Year" ? "selected" : "") . ">4th Year</option>
                  </select>";
            echo "<span class='text-field' data-column='$key' style='display:block;'>$value</span>";
        }else if ($key == "course") {
            // Dropdown for course
            echo "<select class='input-field' data-column='$key' style='display:none;'>
                    <option value='BSIT' " . ($value == "BSIT" ? "selected" : "") . ">BSIT</option>
                    <option value='BSED' " . ($value == "BSED" ? "selected" : "") . ">BSED</option>
                    <option value='BSTM' " . ($value == "BSTM" ? "selected" : "") . ">BSTM</option>
                    <option value='BSHM' " . ($value == "BSHM" ? "selected" : "") . ">BSHM</option>
                    <option value='BSCRIM' " . ($value == "BSCRIM" ? "selected" : "") . ">BSCRIM</option>
                  </select>";
            echo "<span class='text-field' data-column='$key' style='display:block;'>$value</span>";
        } else {
            // Text input for other fields
            echo "<span class='text-field' data-column='$key'>$value</span>";
            echo "<input class='input-field' data-column='$key' type='text' value='$value' style='display:none;'>";
        }
    } else {
        echo htmlspecialchars($value);
    }
    
    echo "</td></tr>";
}
?>


        </table>
    </section>
</main>
    
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


        document.getElementById('editButton').addEventListener('click', function() {
    document.querySelectorAll('.text-field').forEach(el => el.style.display = 'none');
    document.querySelectorAll('.input-field').forEach(el => el.style.display = 'block');
    document.getElementById('editButton').style.display = 'none';
    document.getElementById('saveButton').style.display = 'inline-block';
});

document.getElementById('saveButton').addEventListener('click', function() {
    let updates = {};
    document.querySelectorAll('.input-field').forEach(input => {
        updates[input.getAttribute('data-column')] = input.value;
    });

    fetch('update_student.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ 
            student_id: <?php echo json_encode($student_id); ?>, 
            updates: updates 
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert("Edit Successfully");
            location.reload();
        } else {
            alert("Error: " + data.error);
        }
    })
    .catch(error => {
        console.error("AJAX Error:", error);
        alert("Failed to update student details.");
    });
});


        </script>
</body>
</html>