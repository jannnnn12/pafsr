<?php
// Add this to student_data.php
// First include the risk calculation functions if they're in a separate file
include "risk_calculator.php";

// Add this section to the tab navigation
?>
<div class="tabs">
    <div class="tab active" data-tab="manual-entry">Manual Entry</div>
    <div class="tab" data-tab="batch-upload">Batch Upload</div>
    <div class="tab" data-tab="student-data">Student Data</div>
    <div class="tab" data-tab="data-history">Data History</div>
</div>

<?php
// Add this as a new tab content section
?>
<div class="tab-content" id="student-data">
    <h3>Student Performance & Risk Assessment</h3>
    
    <div class="filters-bar">
        <div class="form-row">
            <div class="form-col">
                <label for="risk-filter">Risk Level:</label>
                <select id="risk-filter" class="form-control">
                    <option value="all">All Levels</option>
                    <option value="high">High Risk</option>
                    <option value="medium">Medium Risk</option>
                    <option value="low">Low Risk</option>
                </select>
            </div>
            <div class="form-col">
                <label for="grade-filter">Grade Level:</label>
                <select id="grade-filter" class="form-control">
                    <option value="all">All Grades</option>
                    <!-- Add your grade options here -->
                </select>
            </div>
            <div class="form-col">
                <label for="data-search">Search:</label>
                <input type="text" id="data-search" class="form-control" placeholder="Search students...">
            </div>
        </div>
    </div>
    
    <div class="table-responsive">
        <table id="student-data-table" class="data-table">
            <thead>
                <tr>
                    <th>Risk Level</th>
                    <th>Student</th>
                    <th>Grade</th>
                    <th>Attendance</th>
                    <th>Assessments</th>
                    <th>Participation</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                // Get student data with risk assessment
                $students = getStudentData($conn);
                
                foreach ($students as $student): 
                    // Determine CSS class for risk level
                    $risk_class = '';
                    switch ($student['risk_level']) {
                        case 'High':
                            $risk_class = 'high-risk';
                            break;
                        case 'Medium':
                            $risk_class = 'medium-risk';
                            break;
                        case 'Low':
                            $risk_class = 'low-risk';
                            break;
                    }
                ?>
                <tr data-risk="<?php echo strtolower($student['risk_level']); ?>" data-grade="<?php echo $student['grade_level']; ?>">
                    <td>
                        <div class="risk-indicator <?php echo $risk_class; ?>">
                            <?php echo $student['risk_level']; ?>
                            <span class="risk-score"><?php echo $student['risk_score']; ?></span>
                        </div>
                    </td>
                    <td>
                        <div class="student-info">
                            <strong><?php echo htmlspecialchars($student['full_name']); ?></strong>
                            <span class="student-id">ID: <?php echo $student['student_id']; ?></span>
                        </div>
                    </td>
                    <td><?php echo htmlspecialchars($student['grade_level']); ?></td>
                    <td>
                        <div class="progress-container">
                            <div class="progress-bar" style="width: <?php echo $student['attendance_percentage']; ?>%"></div>
                            <span class="progress-text"><?php echo $student['attendance_percentage']; ?>%</span>
                        </div>
                        <div class="metric-details">
                            <span>Present: <?php echo $student['attendance_data']['present_days']; ?></span>
                            <span>Absent: <?php echo $student['attendance_data']['absent_days']; ?></span>
                            <span>Late: <?php echo $student['attendance_data']['late_days']; ?></span>
                        </div>
                    </td>
                    <td>
                        <div class="progress-container">
                            <div class="progress-bar" style="width: <?php echo $student['assessment_avg']; ?>%"></div>
                            <span class="progress-text"><?php echo $student['assessment_avg']; ?>%</span>
                        </div>
                        <div class="metric-details">
                            <span>Assessments: <?php echo $student['assessment_count']; ?></span>
                        </div>
                    </td>
                    <td>
                        <div class="participation-stars">
                            <?php 
                            $avg = $student['participation_avg'];
                            for ($i = 1; $i <= 5; $i++) {
                                if ($i <= $avg) {
                                    echo '<i class="fas fa-star"></i>';
                                } elseif ($i - 0.5 <= $avg) {
                                    echo '<i class="fas fa-star-half-alt"></i>';
                                } else {
                                    echo '<i class="far fa-star"></i>';
                                }
                            }
                            ?>
                            <span class="avg-value"><?php echo $student['participation_avg']; ?>/5</span>
                        </div>
                        <div class="metric-details">
                            <span>Entries: <?php echo $student['participation_count']; ?></span>
                        </div>
                    </td>
                    <td>
                        <button class="btn btn-primary btn-sm" onclick="viewStudentDetails(<?php echo $student['student_id']; ?>)">
                            <i class="fas fa-eye"></i> View
                        </button>
                        <button class="btn btn-secondary btn-sm" onclick="addIntervention(<?php echo $student['student_id']; ?>)">
                            <i class="fas fa-clipboard-check"></i> Intervention
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <div class="risk-summary">
        <h4>Risk Level Distribution</h4>
        <div class="risk-counters">
            <?php 
            $high_risk = count(array_filter($students, function($s) { return $s['risk_level'] == 'High'; }));
            $medium_risk = count(array_filter($students, function($s) { return $s['risk_level'] == 'Medium'; }));
            $low_risk = count(array_filter($students, function($s) { return $s['risk_level'] == 'Low'; }));
            $total = count($students);
            ?>
            <div class="risk-counter high-risk">
                <div class="count"><?php echo $high_risk; ?></div>
                <div class="label">High Risk</div>
                <div class="percentage"><?php echo round(($high_risk / $total) * 100); ?>%</div>
            </div>
            <div class="risk-counter medium-risk">
                <div class="count"><?php echo $medium_risk; ?></div>
                <div class="label">Medium Risk</div>
                <div class="percentage"><?php echo round(($medium_risk / $total) * 100); ?>%</div>
            </div>
            <div class="risk-counter low-risk">
                <div class="count"><?php echo $low_risk; ?></div>
                <div class="label">Low Risk</div>
                <div class="percentage"><?php echo round(($low_risk / $total) * 100); ?>%</div>
            </div>
        </div>
    </div>
</div>