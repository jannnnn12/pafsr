<?php
session_start();
if (!isset($_SESSION['user_id'])) {  
    header("Location: login.php");
    exit();
}
include "db_connect.php";
include "risk_calculator.php";

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get student ID from form
    $student_id = $_POST['student_id'];
    
    // Process attendance data
    if (isset($_POST['attendance_date']) && isset($_POST['attendance_status'])) {
        $date = $_POST['attendance_date'];
        $status = $_POST['attendance_status'];
        
        // Insert attendance record
        $stmt = $conn->prepare("INSERT INTO student_attendance (student_id, attendance_date, status) 
                               VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE status = ?");
        $stmt->bind_param("isss", $student_id, $date, $status, $status);
        $stmt->execute();
    }
    
    // Process quiz/exam data
    if (isset($_POST['assessment_type']) && isset($_POST['assessment_date']) && isset($_POST['score']) && isset($_POST['max_score'])) {
        $type = $_POST['assessment_type'];
        $date = $_POST['assessment_date'];
        $score = $_POST['score'];
        $max_score = $_POST['max_score'];
        
        // Insert assessment record
        $stmt = $conn->prepare("INSERT INTO student_assessments (student_id, assessment_type, assessment_date, score, max_score) 
                               VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issdd", $student_id, $type, $date, $score, $max_score);
        $stmt->execute();
    }
    
    // Process participation data
    if (isset($_POST['participation_date']) && isset($_POST['participation_level'])) {
        $date = $_POST['participation_date'];
        $level = $_POST['participation_level'];
        $notes = $_POST['participation_notes'] ?? '';
        
        // Insert participation record
        $stmt = $conn->prepare("INSERT INTO student_participation (student_id, participation_date, level, notes) 
                               VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isis", $student_id, $date, $level, $notes);
        $stmt->execute();
    }
    
    // Set success message
    $_SESSION['message'] = "Student data saved successfully!";
    header("Location: student_data.php");
    exit();
}

// Get list of students for dropdown
$students_query = "SELECT student_id, CONCAT(first_name, ' ', last_name) as full_name FROM students ORDER BY full_name";
$students_result = $conn->query($students_query);

// Get counts for stat cards
$total_records_query = "SELECT 
    (SELECT COUNT(*) FROM student_attendance) + 
    (SELECT COUNT(*) FROM student_assessments) + 
    (SELECT COUNT(*) FROM student_participation) as total";
$total_result = $conn->query($total_records_query);
$total_row = $total_result->fetch_assoc();
$total_records = $total_row['total'];

$attendance_query = "SELECT COUNT(*) as count FROM student_attendance";
$attendance_result = $conn->query($attendance_query);
$attendance_row = $attendance_result->fetch_assoc();
$attendance_count = $attendance_row['count'];

$assessment_query = "SELECT COUNT(*) as count FROM student_assessments";
$assessment_result = $conn->query($assessment_query);
$assessment_row = $assessment_result->fetch_assoc();
$assessment_count = $assessment_row['count'];

$participation_query = "SELECT COUNT(*) as count FROM student_participation";
$participation_result = $conn->query($participation_query);
$participation_row = $participation_result->fetch_assoc();
$participation_count = $participation_row['count'];

// Get recent data entries for history tab
$history_query = "
(SELECT 
    sa.attendance_date as entry_date, 
    CONCAT(s.first_name, ' ', s.last_name) as student_name,
    'Attendance' as data_type,
    sa.status as details,
    'System' as entered_by,
    'attendance' as record_type,
    sa.id as record_id
FROM student_attendance sa
JOIN students s ON sa.student_id = s.student_id
ORDER BY sa.attendance_date DESC
LIMIT 5)

UNION ALL

(SELECT 
    sas.assessment_date as entry_date,
    CONCAT(s.first_name, ' ', s.last_name) as student_name,
    sas.assessment_type as data_type,
    CONCAT('Score: ', sas.score, '/', sas.max_score) as details,
    'System' as entered_by,
    'assessment' as record_type,
    sas.id as record_id
FROM student_assessments sas
JOIN students s ON sas.student_id = s.student_id
ORDER BY sas.assessment_date DESC
LIMIT 5)

UNION ALL

(SELECT 
    sp.participation_date as entry_date,
    CONCAT(s.first_name, ' ', s.last_name) as student_name,
    'Participation' as data_type,
    CONCAT('Level: ', sp.level, ' - ', 
        CASE 
            WHEN sp.level = 1 THEN 'Very Low'
            WHEN sp.level = 2 THEN 'Low'
            WHEN sp.level = 3 THEN 'Average'
            WHEN sp.level = 4 THEN 'Good'
            WHEN sp.level = 5 THEN 'Excellent'
        END) as details,
    'System' as entered_by,
    'participation' as record_type,
    sp.id as record_id
FROM student_participation sp
JOIN students s ON sp.student_id = s.student_id
ORDER BY sp.participation_date DESC
LIMIT 5)

ORDER BY entry_date DESC
LIMIT 10";

$history_result = $conn->query($history_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Data Management | RetentionX</title>
    <link rel="stylesheet" href="../css/student_data.css"/>
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
            <li><a href="#" class="active"> <img src="../img/student-profile.png" alt="List"/>Data Management</a></li>
            <li><a href="interventions.php"> <img src="../img/good-business.png" alt="Recommend"/>Interventions</a></li>
            <li><a href="alert.php"> <img src="../img/warning.png" alt="reports"/>Notification and Alerts</a></li>
            <li><a href="reports.php"> <img src="../img/file.png" alt="reports"/>Reports &amp; History</a></li>
            <li><a href="settings.php"> <img src="../img/cogwheel.png" alt="setting"/>Settings</a></li>
        </ul>
        <button class="logout-btn" id="logoutBtn">Log Out</button>
    </div>
    
    <div class="mobile-menu-btn">
        <button id="menuButton">
            <i class="fas fa-bars"></i>
        </button>
    </div>
    
    <div class="main-content">
        <div class="page-header">
            <h1 class="page-title">Data Management</h1>
            <div>
                <button class="btn btn-primary"><i class="fas fa-upload"></i> Import Data</button>
                <button class="btn btn-secondary"><i class="fas fa-download"></i> Export</button>
            </div>
        </div>
        
        <?php if (isset($_SESSION['message'])): ?>
            <div class="success-message">
                <?php 
                    echo $_SESSION['message']; 
                    unset($_SESSION['message']);
                ?>
            </div>
        <?php endif; ?>
        
        <div class="dashboard-cards">
            <div class="stat-card">
                <div class="stat-value"><?php echo $total_records; ?></div>
                <div class="stat-label">Total Records</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?php echo $attendance_count; ?></div>
                <div class="stat-label">Attendance Records</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?php echo $assessment_count; ?></div>
                <div class="stat-label">Assessment Records</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?php echo $participation_count; ?></div>
                <div class="stat-label">Participation Records</div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Manage Student Data</h2>
            </div>
            
            <div class="tabs">
                <div class="tab active" data-tab="manual-entry">Manual Entry</div>
                <div class="tab" data-tab="batch-upload">Batch Upload</div>
                <div class="tab" data-tab="student-data">Student Data</div>
                <div class="tab" data-tab="data-history">Data History</div>
            </div>
            
            <div class="tab-content active" id="manual-entry">
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="student_id">Select Student:</label>
                        <select class="form-control" id="student_id" name="student_id" required>
                            <option value="">-- Select Student --</option>
                            <?php while($student = $students_result->fetch_assoc()): ?>
                                <option value="<?php echo $student['student_id']; ?>">
                                    <?php echo htmlspecialchars($student['full_name']); ?> (ID: <?php echo $student['student_id']; ?>)
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    
                    <div class="tabs">
                        <div class="tab active" data-tab="attendance">Attendance</div>
                        <div class="tab" data-tab="assessments">Assessments</div>
                        <div class="tab" data-tab="participation">Participation</div>
                    </div>
                    
                    <div class="tab-content active" id="attendance">
                        <h3>Attendance Record</h3>
                        <div class="form-row">
                            <div class="form-col">
                                <label for="attendance_date">Date:</label>
                                <input type="date" class="form-control" id="attendance_date" name="attendance_date" required>
                            </div>
                            <div class="form-col">
                                <label for="attendance_status">Status:</label>
                                <select class="form-control" id="attendance_status" name="attendance_status" required>
                                    <option value="present">Present</option>
                                    <option value="absent">Absent</option>
                                    <option value="late">Late</option>
                                    <option value="excused">Excused</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="tab-content" id="assessments">
                        <h3>Assessment/Quiz Data</h3>
                        <div class="form-row">
                            <div class="form-col">
                                <label for="assessment_type">Assessment Type:</label>
                                <select class="form-control" id="assessment_type" name="assessment_type" required>
                                    <option value="quiz">Quiz</option>
                                    <option value="exam">Exam</option>
                                    <option value="assignment">Assignment</option>
                                    <option value="project">Project</option>
                                </select>
                            </div>
                            <div class="form-col">
                                <label for="assessment_date">Date:</label>
                                <input type="date" class="form-control" id="assessment_date" name="assessment_date" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-col">
                                <label for="score">Score:</label>
                                <input type="number" class="form-control" id="score" name="score" min="0" step="0.01" required>
                            </div>
                            <div class="form-col">
                                <label for="max_score">Maximum Score:</label>
                                <input type="number" class="form-control" id="max_score" name="max_score" min="0" step="0.01" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="tab-content" id="participation">
                        <h3>Class Participation</h3>
                        <div class="form-row">
                            <div class="form-col">
                                <label for="participation_date">Date:</label>
                                <input type="date" class="form-control" id="participation_date" name="participation_date" required>
                            </div>
                            <div class="form-col">
                                <label for="participation_level">Participation Level:</label>
                                <select class="form-control" id="participation_level" name="participation_level" required>
                                    <option value="1">1 - Very Low</option>
                                    <option value="2">2 - Low</option>
                                    <option value="3">3 - Average</option>
                                    <option value="4">4 - Good</option>
                                    <option value="5">5 - Excellent</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="participation_notes">Notes:</label>
                            <textarea class="form-control" id="participation_notes" name="participation_notes" rows="3"></textarea>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Save Data</button>
                        <button type="reset" class="btn btn-secondary">Reset</button>
                    </div>
                </form>
            </div>
            
            <div class="tab-content" id="batch-upload">
                <h3>Upload Data Files</h3>
                <div class="upload-area">
                    <i class="fas fa-cloud-upload-alt" style="font-size: 48px; color: #6b7280; margin-bottom: 15px;"></i>
                    <p>Drag & drop files here or click to browse</p>
                    <input type="file" id="file-upload" style="display: none">
                    <button class="btn btn-primary" onclick="document.getElementById('file-upload').click()">Select Files</button>
                    <p class="mt-2">Supported formats: CSV, Excel</p>
                </div>
                
                <div class="form-group">
                    <label for="data-type">Data Type:</label>
                    <select class="form-control" id="data-type">
                        <option value="attendance">Attendance</option>
                        <option value="assessments">Assessments</option>
                        <option value="participation">Participation</option>
                    </select>
                </div>
                
                <button class="btn btn-primary">Upload Data</button>
            </div>
            
            <div class="tab-content" id="student-data">
    <h3>Student Performance Overview</h3>
    <table>
        <thead>
            <tr>
                <th>Student Name</th>
                <th>Attendance (%)</th>
                <th>Quiz Average</th>
                <th>Overall Status</th>
            </tr>
        </thead>
        <tbody>
            <?php while($student = $students_result->fetch_assoc()): ?>
                <?php 
                    // Fetch attendance percentage
                    $attendance_percentage = $student['total_attendance'] > 0 
                        ? ($student['present_count'] / $student['total_attendance']) * 100 
                        : 0;

                    // Fetch quiz average
                    $quiz_average = $student['total_quiz_scores'] > 0 
                        ? ($student['total_quiz_scores'] / $student['quiz_count']) 
                        : 0;

                    // Determine risk status
                    $status = "Good";
                    if ($attendance_percentage < 75 || $quiz_average < 50) {
                        $status = "<span style='color: red; font-weight: bold;'>At Risk</span>";
                    }
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($student['full_name']); ?></td>
                    <td><?php echo number_format($attendance_percentage, 2); ?>%</td>
                    <td><?php echo number_format($quiz_average, 2); ?>%</td>
                    <td><?php echo $status; ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

            <div class="tab-content" id="data-history">
                <h3>Recent Data Entries</h3>
                <?php if ($history_result && $history_result->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Student</th>
                            <th>Data Type</th>
                            <th>Details</th>
                            <th>Entered By</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($entry = $history_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo date('Y-m-d', strtotime($entry['entry_date'])); ?></td>
                            <td><?php echo htmlspecialchars($entry['student_name']); ?></td>
                            <td><?php echo htmlspecialchars($entry['data_type']); ?></td>
                            <td><?php echo htmlspecialchars($entry['details']); ?></td>
                            <td><?php echo htmlspecialchars($entry['entered_by'] ?? 'System'); ?></td>
                            <td>
                                <button class="btn btn-secondary btn-sm" onclick="editRecord('<?php echo $entry['record_type']; ?>', <?php echo $entry['record_id']; ?>)"><i class="fas fa-edit"></i></button>
                                <button class="btn btn-secondary btn-sm" onclick="deleteRecord('<?php echo $entry['record_type']; ?>', <?php echo $entry['record_id']; ?>)"><i class="fas fa-trash"></i></button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                <div class="pagination">
                    <button class="active">1</button>
                    <button>2</button>
                    <button>3</button>
                    <button><i class="fas fa-chevron-right"></i></button>
                </div>
                <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-database"></i>
                    <h3>No records found</h3>
                    <p>Start adding student data to see records here.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <script>
        // Toggle sidebar on mobile
        document.getElementById('menuButton').addEventListener('click', function() {
            var sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('show');
        });
        
        // User profile dropdown
        document.addEventListener("DOMContentLoaded", function () {
            const userProfileButton = document.getElementById("userProfileButton");
            const userProfileDropdown = document.getElementById("userProfileDropdown");
            const closeDropdown = document.getElementById("closeDropdown");

            // Toggle dropdown on click
            userProfileButton.addEventListener("click", function () {
                userProfileDropdown.classList.toggle("show");
            });

            // Close dropdown
            closeDropdown.addEventListener("click", function () {
                userProfileDropdown.classList.remove("show");
            });

            // Close when clicking outside
            document.addEventListener("click", function (event) {
                if (!userProfileButton.contains(event.target) && !userProfileDropdown.contains(event.target)) {
                    userProfileDropdown.classList.remove("show");
                }
            });
            
            // Tab switching for main tabs
            const tabs = document.querySelectorAll('.tabs .tab');
            tabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    // Get the parent tabs container
                    const tabsContainer = this.parentElement;
                    // Remove active class from all tabs in this container
                    tabsContainer.querySelectorAll('.tab').forEach(t => {
                        t.classList.remove('active');
                    });
                    // Add active class to clicked tab
                    this.classList.add('active');
                    
                    // If these are the main tabs
                    if (tabsContainer === document.querySelector('.card .tabs')) {
                        // Hide all tab content
                        document.querySelectorAll('.card > .tab-content').forEach(content => {
                            content.classList.remove('active');
                        });
                        // Show the corresponding tab content
                        document.getElementById(this.getAttribute('data-tab')).classList.add('active');
                    } else {
                        // For nested tabs within manual entry
                        document.querySelectorAll('#manual-entry .tab-content').forEach(content => {
                            content.classList.remove('active');
                        });
                        document.getElementById(this.getAttribute('data-tab')).classList.add('active');
                    }
                });
            });
        });
        
        // Logout confirmation
        document.getElementById("logoutBtn").addEventListener("click", function() {
            let confirmLogout = confirm("Are you sure you want to log out?");
            if (confirmLogout) {
                window.location.href = "login.php"; // Redirect to login page
            }
        });

        // Function to handle record editing (placeholder)
        function editRecord(recordType, recordId) {
            // You can implement this to open a modal or redirect to an edit page
            alert('Edit ' + recordType + ' record ID: ' + recordId);
        }

        // Function to handle record deletion
        function deleteRecord(recordType, recordId) {
            if (confirm('Are you sure you want to delete this record?')) {
                // Create a form to submit the delete request
                let form = document.createElement('form');
                form.method = 'POST';
                form.action = 'delete_record.php';
                
                let typeInput = document.createElement('input');
                typeInput.type = 'hidden';
                typeInput.name = 'record_type';
                typeInput.value = recordType;
                form.appendChild(typeInput);
                
                let idInput = document.createElement('input');
                idInput.type = 'hidden';
                idInput.name = 'record_id';
                idInput.value = recordId;
                form.appendChild(idInput);
                
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
</body>
</html>