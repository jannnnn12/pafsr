<?php
// Add this to your existing file or create a new include file

function calculateStudentRisk($student_id, $conn) {
    // Get attendance data
    $attendance_query = "
        SELECT 
            COUNT(*) as total_days,
            SUM(CASE WHEN status = 'absent' THEN 1 ELSE 0 END) as absent_days,
            SUM(CASE WHEN status = 'late' THEN 1 ELSE 0 END) as late_days
        FROM student_attendance 
        WHERE student_id = ? AND attendance_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
    
    $stmt = $conn->prepare($attendance_query);
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $attendance_result = $stmt->get_result();
    $attendance_data = $attendance_result->fetch_assoc();
    
    // Calculate attendance risk (30% weight)
    $attendance_risk = 0;
    if ($attendance_data['total_days'] > 0) {
        $absence_rate = ($attendance_data['absent_days'] / $attendance_data['total_days']) * 100;
        $late_rate = ($attendance_data['late_days'] / $attendance_data['total_days']) * 100;
        
        // Absence rate over 20% is high risk
        if ($absence_rate > 20) {
            $attendance_risk = 3;
        } elseif ($absence_rate > 10) {
            $attendance_risk = 2;
        } elseif ($absence_rate > 5 || $late_rate > 15) {
            $attendance_risk = 1;
        }
    }
    
    // Get assessment data - recent performance
    $assessment_query = "
        SELECT 
            AVG(score/max_score * 100) as avg_percentage,
            COUNT(*) as total_assessments
        FROM student_assessments
        WHERE student_id = ? AND assessment_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
    
    $stmt = $conn->prepare($assessment_query);
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $assessment_result = $stmt->get_result();
    $assessment_data = $assessment_result->fetch_assoc();
    
    // Calculate assessment risk (50% weight)
    $assessment_risk = 0;
    if ($assessment_data['total_assessments'] > 0) {
        $avg_score = $assessment_data['avg_percentage'];
        
        // Average below 60% is high risk
        if ($avg_score < 60) {
            $assessment_risk = 3;
        } elseif ($avg_score < 70) {
            $assessment_risk = 2;
        } elseif ($avg_score < 80) {
            $assessment_risk = 1;
        }
    }
    
    // Get participation data
    $participation_query = "
        SELECT 
            AVG(level) as avg_level,
            COUNT(*) as total_entries
        FROM student_participation
        WHERE student_id = ? AND participation_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
    
    $stmt = $conn->prepare($participation_query);
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $participation_result = $stmt->get_result();
    $participation_data = $participation_result->fetch_assoc();
    
    // Calculate participation risk (20% weight)
    $participation_risk = 0;
    if ($participation_data['total_entries'] > 0) {
        $avg_level = $participation_data['avg_level'];
        
        // Average below 2 is high risk
        if ($avg_level < 2) {
            $participation_risk = 3;
        } elseif ($avg_level < 3) {
            $participation_risk = 2;
        } elseif ($avg_level < 4) {
            $participation_risk = 1;
        }
    }
    
    // Calculate overall risk score (weighted average)
    $overall_risk_score = 
        ($attendance_risk * 0.3) + 
        ($assessment_risk * 0.5) + 
        ($participation_risk * 0.2);
    
    // Determine risk level
    $risk_level = 'Low';
    if ($overall_risk_score >= 2) {
        $risk_level = 'High';
    } elseif ($overall_risk_score >= 1) {
        $risk_level = 'Medium';
    }
    
    // Assign a numerical score for sorting
    $risk_score = round($overall_risk_score * 10) / 10;
    
    return [
        'risk_level' => $risk_level,
        'risk_score' => $risk_score,
        'attendance_risk' => $attendance_risk,
        'assessment_risk' => $assessment_risk,
        'participation_risk' => $participation_risk,
        'absence_rate' => $absence_rate ?? 0,
        'avg_score' => $avg_score ?? 0,
        'avg_participation' => $avg_level ?? 0
    ];
}

// Function to get detailed student data for the table
function getStudentData($conn) {
    $query = "
        SELECT 
            s.student_id,
            s.first_name,
            s.last_name,
            CONCAT(s.first_name, ' ', s.last_name) as full_name,
            s.email,
            s.enrollment_date,
            s.program,
            s.grade_level
        FROM students s
        ORDER BY s.last_name, s.first_name";
    
    $result = $conn->query($query);
    $students = [];
    
    while ($row = $result->fetch_assoc()) {
        $student_id = $row['student_id'];
        
        // Calculate risk level
        $risk_data = calculateStudentRisk($student_id, $conn);
        
        // Get attendance summary
        $attendance_query = "
            SELECT 
                COUNT(*) as total_days,
                SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) as present_days,
                SUM(CASE WHEN status = 'absent' THEN 1 ELSE 0 END) as absent_days,
                SUM(CASE WHEN status = 'late' THEN 1 ELSE 0 END) as late_days,
                SUM(CASE WHEN status = 'excused' THEN 1 ELSE 0 END) as excused_days
            FROM student_attendance 
            WHERE student_id = ?";
        
        $stmt = $conn->prepare($attendance_query);
        $stmt->bind_param("i", $student_id);
        $stmt->execute();
        $attendance_result = $stmt->get_result();
        $attendance_data = $attendance_result->fetch_assoc();
        
        // Get assessment average
        $assessment_query = "
            SELECT 
                AVG(score/max_score * 100) as avg_percentage,
                COUNT(*) as total_assessments
            FROM student_assessments
            WHERE student_id = ?";
        
        $stmt = $conn->prepare($assessment_query);
        $stmt->bind_param("i", $student_id);
        $stmt->execute();
        $assessment_result = $stmt->get_result();
        $assessment_data = $assessment_result->fetch_assoc();
        
        // Get participation average
        $participation_query = "
            SELECT 
                AVG(level) as avg_level,
                COUNT(*) as total_entries
            FROM student_participation
            WHERE student_id = ?";
        
        $stmt = $conn->prepare($participation_query);
        $stmt->bind_param("i", $student_id);
        $stmt->execute();
        $participation_result = $stmt->get_result();
        $participation_data = $participation_result->fetch_assoc();
        
        // Calculate attendance percentage
        $attendance_percentage = 0;
        if ($attendance_data['total_days'] > 0) {
            $attendance_percentage = round(($attendance_data['present_days'] / $attendance_data['total_days']) * 100, 1);
        }
        
        // Add all data to student array
        $students[] = array_merge($row, [
            'risk_level' => $risk_data['risk_level'],
            'risk_score' => $risk_data['risk_score'],
            'attendance_percentage' => $attendance_percentage,
            'attendance_data' => $attendance_data,
            'assessment_avg' => round($assessment_data['avg_percentage'] ?? 0, 1),
            'assessment_count' => $assessment_data['total_assessments'] ?? 0,
            'participation_avg' => round($participation_data['avg_level'] ?? 0, 1),
            'participation_count' => $participation_data['total_entries'] ?? 0
        ]);
    }
    
    return $students;
}