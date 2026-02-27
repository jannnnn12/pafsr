<?php
session_start(); // Start the session to access session variables
include 'db_connect.php'; // Include your database connection file

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>User Profile | RetentionX</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&family=Open+Sans:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="../img/logo1.png">
    <style>
        /* Reset and Base Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            transition: all 0.3s ease;
        }
        
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #111;
            color: #fff;
            display: flex;
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

        /* Sidebar Styles */
        .sidebar {
            width: 250px;
            min-height: 100vh;
            background-color: #000;
            color: #fff;
            padding: 20px;
            animation: slideIn 0.5s ease forwards;
            box-shadow: 0 0 5px 1px rgba(255, 255, 255, 0.1);
        }

        .sidebar h1 {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 2rem;
            text-align: center;
            color: #fff;
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
            animation: dropdownFade 0.3s ease forwards;
        }

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

        .user-profile-dropdown .dropdown-header {
            background-color: #f3f4f6;
            padding: 1rem;
            text-align: center;
            border-bottom: 1px solid #e5e7eb;
            position: relative;
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
            border-radius: 0.25rem;
        }

        .sidebar-nav a.active {
            background-color: #374151;
        }
        
        .sidebar-nav a:hover {
            background-color: #374151;
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

        /* Main Content Styles */
        .main-content {
            flex: 1;
            padding: 2rem;
            overflow-y: auto;
            background-color: #111;
        }

        .page-title {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid #333;
        }

        /* Profile Sections */
        .section {
            background-color: #1a1a1a;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 2rem; /* Increased margin between sections */
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 0.75rem;
            margin-bottom: 1.5rem; /* Increased margin below header */
            border-bottom: 1px solid #333;
        }

        .section-title {
            font-size: 1.2rem;
            font-weight: 600;
            display: flex;
            align-items: center;
        }

        .section-title i {
            margin-right: 0.5rem;
            color: #3b82f6;
        }

        .section-content {
            display: flex;
            flex-wrap: wrap;
        }

        .profile-picture-section {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            width: 100%;
            padding: 1rem 0; /* Added padding */
        }

        .profile-picture-container {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            overflow: hidden;
            margin-bottom: 1.5rem; /* Increased margin */
            border: 3px solid #3b82f6;
            position: relative;
        }

        .profile-picture-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .profile-picture-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .profile-picture-container:hover .profile-picture-overlay {
            opacity: 1;
        }

        .profile-picture-actions {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.5rem;
        }

        .file-upload {
            display: none;
        }

        .profile-info {
            width: 100%;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem; /* Increased gap between form items */
            margin-bottom: 1rem; /* Added margin at the bottom */
        }

        .form-group {
            margin-bottom: 1.5rem; /* Increased spacing between form groups */
        }

        .form-group label {
            display: block;
            margin-bottom: 0.75rem; /* Increased label spacing */
            font-size: 0.9rem;
            color: #a0aec0;
        }

        .form-group input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #333;
            border-radius: 0.25rem;
            background-color: #222;
            color: #fff;
            font-size: 0.9rem;
        }

        .form-group input:focus {
            outline: none;
            border-color: #3b82f6;
        }

        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 30px;
        }

        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .toggle-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #444;
            transition: .4s;
            border-radius: 30px;
        }

        .toggle-slider:before {
            position: absolute;
            content: "";
            height: 22px;
            width: 22px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked + .toggle-slider {
            background-color: #3b82f6;
        }

        input:checked + .toggle-slider:before {
            transform: translateX(30px);
        }

        .button {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 0.25rem;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .primary-button {
            background-color: #3b82f6;
            color: white;
        }

        .primary-button:hover {
            background-color: #2563eb;
        }

        .secondary-button {
            background-color: transparent;
            color: #3b82f6;
            border: 1px solid #3b82f6;
        }

        .secondary-button:hover {
            background-color: rgba(59, 130, 246, 0.1);
        }

        .section-actions {
            display: flex;
            justify-content: flex-end;
            margin-top: 1.5rem; /* Increased top margin */
            gap: 0.75rem;
        }

       /* Notification Settings */
.settings-container {
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
    margin-bottom: 1.5rem;
}

.settings-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.25rem;
    border-radius: 8px;
    background-color: #222;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

.settings-info {
    flex: 1;
    padding-right: 1.5rem;
}

.settings-title {
    font-weight: 500;
    margin-bottom: 0.5rem;
    font-size: 1rem;
    color: #fff;
}

.settings-description {
    font-size: 0.875rem;
    color: #a0aec0;
    line-height: 1.4;
}

.toggle-switch {
    position: relative;
    display: inline-block;
    width: 60px;
    height: 30px;
    flex-shrink: 0;
}

.toggle-switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.toggle-slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #444;
    transition: .4s;
    border-radius: 30px;
}

.toggle-slider:before {
    position: absolute;
    content: "";
    height: 22px;
    width: 22px;
    left: 4px;
    bottom: 4px;
    background-color: white;
    transition: .4s;
    border-radius: 50%;
}

input:checked + .toggle-slider {
    background-color: #3b82f6;
}

input:checked + .toggle-slider:before {
    transform: translateX(30px);
}

/* Activity Styles */
.activity-list {
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
}

.activity-item {
    display: flex;
    align-items: center;
    padding: 1.25rem;
    border-radius: 8px;
    background-color: #222;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

.activity-icon {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    background-color: #3b82f6;
    display: flex;
    justify-content: center;
    align-items: center;
    margin-right: 1.25rem;
    flex-shrink: 0;
    box-shadow: 0 2px 5px rgba(59, 130, 246, 0.3);
}

.activity-icon i {
    color: #fff;
    font-size: 1.25rem;
}

.activity-content {
    flex: 1;
}

.activity-title {
    font-weight: 500;
    margin-bottom: 0.5rem;
    font-size: 1rem;
    color: #fff;
}

.activity-time {
    font-size: 0.875rem;
    color: #a0aec0;
}

/* Button styles */
.primary-button {
    background-color: #3b82f6;
    color: white;
    padding: 0.75rem 1.5rem;
    border: none;
    border-radius: 0.375rem;
    font-weight: 500;
    cursor: pointer;
    transition: background-color 0.3s;
}

.primary-button:hover {
    background-color: #2563eb;
}

.section-actions {
    display: flex;
    justify-content: flex-end;
    margin-top: 1.5rem;
}

        /* Mobile Menu */
        .mobile-menu-btn {
            display: none;
            padding: 1rem;
            position: fixed;
            top: 1rem;
            left: 1rem;
            z-index: 100;
            background-color: #111;
            border-radius: 50%;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        /* Responsive Styles */
        @media (max-width: 1024px) {
            .sidebar {
                display: none;
                position: fixed;
                z-index: 100;
                height: 100vh;
                overflow-y: auto;
            }
            
            .sidebar.show {
                display: block;
            }
            
            .mobile-menu-btn {
                display: block;
            }
            
            .main-content {
                padding: 1rem;
            }
            
            .profile-info {
                grid-template-columns: 1fr;
            }
        }

        /* Success and Error Messages */
        .message {
            padding: 1rem;
            margin: 1rem 0;
            border-radius: 0.25rem;
            display: none;
            animation: fadeIn 0.3s ease forwards;
        }

        .success-message {
            background-color: rgba(16, 185, 129, 0.1);
            border: 1px solid #10b981;
            color: #10b981;
        }

        .error-message {
            background-color: rgba(239, 68, 68, 0.1);
            border: 1px solid #ef4444;
            color: #ef4444;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Form collapse/expand animations */
        .collapsible-content {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-out;
        }

        .collapsible-content.open {
            max-height: 1000px;
        }

        .collapse-icon {
            transition: transform 0.3s ease;
        }

        .collapse-icon.rotated {
            transform: rotate(180deg);
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <h1>RetentionX</h1>
        <div class="user-profile">
            <div class="user-profile-button" id="userProfileButton">
                <img src="../img/user.png" alt="User profile picture"/>
                <span class="user-profile-name">Sam Robinson</span>
                <i class="fas fa-chevron-down"></i>
            </div>
            <div class="user-profile-dropdown" id="userProfileDropdown">
                <div class="dropdown-header">
                    <span class="close-dropdown" id="closeDropdown">&times;</span>
                    <img src="../img/user.png" alt="User profile"/>
                    <div class="user-name">Sam Robinson</div>
                    <div class="user-email">sam.robinson2000@gmail.com</div>
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
                    <img src="../img/startup.png" alt="Dashboard icon"/>
                    Dashboard
                </a>
            </li>
            <li>
                <a href="viewstudent.php">
                    <img src="../img/checklist.png" alt="Student list icon"/>
                    Student List
                </a>
            </li>
            <li>
                <a href="student_data.php">
                    <img src="../img/student-profile.png" alt="Data management icon"/>
                    Data Management
                </a>
            </li>
            <li>
                <a href="interventions.php">
                    <img src="../img/good-business.png" alt="Interventions icon"/>
                    Interventions
                </a>
            </li>
            <li>
                <a href="alert.php">
                    <img src="../img/warning.png" alt="Alerts icon"/>
                    Notification and Alerts
                </a>
            </li>
            <li>
                <a href="reports.php">
                    <img src="../img/file.png" alt="Reports icon"/>
                    Reports &amp; History
                </a>
            </li>
            <li>
                <a href="#" class="active">
                    <img src="../img/cogwheel.png" alt="Settings icon"/>
                    Settings
                </a>
            </li>
        </ul>
        <button class="logout-btn" id="logoutBtn">Log Out</button>
    </div>

    <!-- Mobile menu button -->
    <div class="mobile-menu-btn" id="mobileMenuBtn">
        <i class="fas fa-bars"></i>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <h1 class="page-title">My Profile</h1>
        
        <!-- Message containers for feedback -->
        <div id="successMessage" class="message success-message"></div>
        <div id="errorMessage" class="message error-message"></div>

        <!-- Profile Picture Section -->
        <div class="section">
            <div class="section-header">
                <h2 class="section-title"><i class="fas fa-image"></i> Profile Picture</h2>
                <button class="collapse-toggle">
                    <i class="fas fa-chevron-down collapse-icon"></i>
                </button>
            </div>
            <div class="section-content collapsible-content open">
                <div class="profile-picture-section">
                    <div class="profile-picture-container">
                        <img src="../img/user.png" alt="Profile picture" id="profileImage"/>
                        <div class="profile-picture-overlay">
                            <label for="profilePictureUpload" class="button primary-button">
                                <i class="fas fa-camera"></i> Change
                            </label>
                        </div>
                    </div>
                    <input type="file" id="profilePictureUpload" class="file-upload" accept="image/*"/>
                    <p>Upload a new profile picture (Maximum size: 5MB)</p>
                    <div class="section-actions">
                        <button class="button secondary-button" id="removeProfilePicture">Remove</button>
                        <button class="button primary-button" id="saveProfilePicture">Save Picture</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Personal Information Section -->
        <div class="section">
    <div class="section-header">
        <h2 class="section-title"><i class="fas fa-user"></i> Personal Information</h2>
        <button class="collapse-toggle">
            <i class="fas fa-chevron-down collapse-icon"></i>
        </button>
    </div>
    <div class="section-content collapsible-content open">
            <div class="profile-info">
                <div class="form-group">
                    <label for="fullName">Full Name</label>
                    <input type="text" id="fullName" value="<?php echo htmlspecialchars($teacher['first_name'] . ' ' . $user['last_name']); ?>" disabled />
                </div>
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" value="<?php echo htmlspecialchars($user['username']); ?>" disabled />
                </div>
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" value="<?php echo htmlspecialchars($user['email']); ?>" disabled />
                </div>
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" id="phone" value="<?php echo htmlspecialchars($user['phone_number']); ?>" disabled />
                </div>
                <div class="form-group">
                    <label for="role">Role</label>
                    <input type="text" id="role" value="<?php echo htmlspecialchars($user['role']); ?>" disabled />
                </div>
                <div class="form-group">
                    <label for="department">Department</label>
                    <input type="text" id="department" value="<?php echo htmlspecialchars($user['department']); ?>" disabled />
                </div>
            </div>
    </div>
</div>
        <!-- Security Section -->
        <div class="section">
            <div class="section-header">
                <h2 class="section-title"><i class="fas fa-lock"></i> Security Settings</h2>
                <button class="collapse-toggle">
                    <i class="fas fa-chevron-down collapse-icon"></i>
                </button>
            </div>
            <div class="section-content collapsible-content open">
                <div class="profile-info">
                    <div class="form-group">
                        <label for="currentPassword">Current Password</label>
                        <input type="password" id="currentPassword" placeholder="Enter your current password"/>
                    </div>
                    <div class="form-group">
                        <label for="newPassword">New Password</label>
                        <input type="password" id="newPassword" placeholder="Enter your new password"/>
                    </div>
                    <div class="form-group">
                        <label for="confirmPassword">Confirm New Password</label>
                        <input type="password" id="confirmPassword" placeholder="Confirm your new password"/>
                    </div>
                </div>
                <div class="section-actions">
                    <button class="button primary-button" id="changePassword">Update Password</button>
                </div>
            </div>
        </div>

        <!-- Notification Settings -->
        <div class="section">
            <div class="section-header">
                <h2 class="section-title"><i class="fas fa-bell"></i> Notification Settings</h2>
                <button class="collapse-toggle">
                    <i class="fas fa-chevron-down collapse-icon"></i>
                </button>
            </div>
            <div class="section-content collapsible-content open">
                <div class="settings-item">
                    <div class="settings-info">
                        <h3 class="settings-title">Email Notifications</h3>
                        <p class="settings-description">Receive notifications via email</p>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" checked>
                        <span class="toggle-slider"></span>
                    </label>
                </div>
                <div class="settings-item">
                    <div class="settings-info">
                        <h3 class="settings-title">SMS Alerts</h3>
                        <p class="settings-description">Receive important alerts via SMS</p>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" checked>
                        <span class="toggle-slider"></span>
                    </label>
                </div>
                <div class="settings-item">
                    <div class="settings-info">
                        <h3 class="settings-title">Student Updates</h3>
                        <p class="settings-description">Get notified about student activities</p>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox">
                        <span class="toggle-slider"></span>
                    </label>
                </div>
                <div class="settings-item">
                    <div class="settings-info">
                        <h3 class="settings-title">System Notifications</h3>
                        <p class="settings-description">Updates about system maintenance and features</p>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" checked>
                        <span class="toggle-slider"></span>
                    </label>
                </div>
                <div class="section-actions">
                    <button class="button primary-button" id="saveNotificationSettings">Save Settings</button>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="section">
            <div class="section-header">
                <h2 class="section-title"><i class="fas fa-history"></i> Recent Activity</h2>
                <button class="collapse-toggle">
                    <i class="fas fa-chevron-down collapse-icon"></i>
                </button>
            </div>
            <div class="section-content collapsible-content open">
                <div class="activity-item">
                    <div class="activity-icon">
                        <i class="fas fa-sign-in-alt"></i>
                    </div>
                    <div class="activity-content">
                        <h3 class="activity-title">Logged in to the system</h3>
                        <p class="activity-time">Today at 9:30 AM</p>
                    </div>
                </div>
                <div class="activity-item">
                    <div class="activity-icon">
                        <i class="fas fa-user-edit"></i>
                    </div>
                    <div class="activity-content">
                        <h3 class="activity-title">Updated profile information</h3>
                        <p class="activity-time">Yesterday at 4:15 PM</p>
                    </div>
                </div>
                <div class="activity-item">
                    <div class="activity-icon">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <div class="activity-content">
                        <h3 class="activity-title">Generated a student report</h3>
                        <p class="activity-time">March 28, 2025 at 11:20 AM</p>
                    </div>
                </div>
                <div class="activity-item">
                    <div class="activity-icon">
                        <i class="fas fa-bell"></i>
                    </div>
                    <div class="activity-content">
                        <h3 class="activity-title">Changed notification settings</h3>
                        <p class="activity-time">March 25, 2025 at 2:45 PM</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>

   // Toggle Sidebar on Mobile
document.getElementById('mobileMenuBtn').addEventListener('click', function() {
    document.getElementById('sidebar').classList.toggle('show');
});

// Toggle User Profile Dropdown
document.getElementById('userProfileButton').addEventListener('click', function() {
    document.getElementById('userProfileDropdown').classList.toggle('show');
});

// Close Dropdown when X is clicked
document.getElementById('closeDropdown').addEventListener('click', function(e) {
    e.stopPropagation();
    document.getElementById('userProfileDropdown').classList.remove('show');
});

// Logout Button
document.getElementById('logoutBtn').addEventListener('click', function() {
    if (confirm('Are you sure you want to log out?')) {
        // Redirect to login page
        window.location.href = 'login.php';
    }
});

// Close dropdown when clicking outside
document.addEventListener('click', function(event) {
    const dropdown = document.getElementById('userProfileDropdown');
    const button = document.getElementById('userProfileButton');
    
    if (!button.contains(event.target) && !dropdown.contains(event.target)) {
        dropdown.classList.remove('show');
    }
});

// Handle profile picture upload
document.getElementById('profilePictureUpload').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        // Check file size (5MB max)
        if (file.size > 5 * 1024 * 1024) {
            showMessage('Error: File size exceeds 5MB limit', 'error');
            return;
        }
        
        // Check file type
        if (!file.type.match('image.*')) {
            showMessage('Error: Please select an image file', 'error');
            return;
        }
        
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('profileImage').src = e.target.result;
        };
        reader.readAsDataURL(file);
    }
});

// Remove profile picture
document.getElementById('removeProfilePicture').addEventListener('click', function() {
    document.getElementById('profileImage').src = '../img/user.png';
    document.getElementById('profilePictureUpload').value = '';
    showMessage('Profile picture removed', 'success');
});

// Save profile picture
document.getElementById('saveProfilePicture').addEventListener('click', function() {
    if (document.getElementById('profilePictureUpload').files.length > 0) {
        // Here you would normally send the file to the server
        // For demo purposes, we'll just show a success message
        showMessage('Profile picture updated successfully', 'success');
    } else {
        showMessage('Please select a file first', 'error');
    }
});

// Save personal information
document.getElementById('savePersonalInfo').addEventListener('click', function() {
    // Here you would normally send the updated info to the server
    // For demo purposes, we'll just show a success message
    showMessage('Personal information updated successfully', 'success');
});

// Change password
document.getElementById('changePassword').addEventListener('click', function() {
    const currentPassword = document.getElementById('currentPassword').value;
    const newPassword = document.getElementById('newPassword').value;
    const confirmPassword = document.getElementById('confirmPassword').value;
    
    if (!currentPassword || !newPassword || !confirmPassword) {
        showMessage('Please fill in all password fields', 'error');
        return;
    }
    
    if (newPassword !== confirmPassword) {
        showMessage('New passwords do not match', 'error');
        return;
    }
    
    if (newPassword.length < 8) {
        showMessage('Password must be at least 8 characters long', 'error');
        return;
    }
    
    // Here you would normally validate the current password and update
    // For demo purposes, we'll just show a success message
    showMessage('Password updated successfully', 'success');
    
    // Clear password fields
    document.getElementById('currentPassword').value = '';
    document.getElementById('newPassword').value = '';
    document.getElementById('confirmPassword').value = '';
});

// Save notification settings
document.getElementById('saveNotificationSettings').addEventListener('click', function() {
    // Here you would normally send the settings to the server
    // For demo purposes, we'll just show a success message
    showMessage('Notification settings saved', 'success');
});

// Section collapse/expand functionality
const toggleButtons = document.querySelectorAll('.collapse-toggle');
toggleButtons.forEach(button => {
    button.addEventListener('click', function() {
        const content = this.parentNode.nextElementSibling;
        const icon = this.querySelector('.collapse-icon');
        
        content.classList.toggle('open');
        icon.classList.toggle('rotated');
    });
});

// Helper function to show messages
function showMessage(message, type) {
    const messageElement = document.getElementById(type === 'error' ? 'errorMessage' : 'successMessage');
    messageElement.textContent = message;
    messageElement.style.display = 'block';
    
    // Auto hide after 5 seconds
    setTimeout(function() {
        messageElement.style.display = 'none';
    }, 5000);
}
</script>
