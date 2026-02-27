<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Admin Dashboard - Teachers</title>
    <link rel="stylesheet" href="../css/admin_dashboard.css"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <link rel="icon" type="image/x-icon" href="../img/logo1.png">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<div class="sidebar">
    <h1>Admin Panel</h1>
    <ul class="sidebar-nav">
        <li><a href="#" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
        <li><a href="manage_teachers.php"><i class="fas fa-user-tie"></i> Manage Teachers</a></li>
        <li><a href="messages.php"><i class="fas fa-envelope"></i> Messages</a></li>
        <li><a href="reports.php"><i class="fas fa-chart-bar"></i> Reports</a></li>
        <li><a href="settings.php"><i class="fas fa-cog"></i> Settings</a></li>
    </ul>
    <button class="logout-btn">Log Out</button>
</div>

<div class="main-content">
    <h2>Teacher Overview</h2>
    <div class="overview-grid">
        <div class="overview-card"><i class="fas fa-users"></i> Total Teachers: <span>50</span></div>
        <div class="overview-card"><i class="fas fa-check-circle"></i> Verified: <span>30</span></div>
        <div class="overview-card"><i class="fas fa-clock"></i> Pending: <span>15</span></div>
        <div class="overview-card"><i class="fas fa-times-circle"></i> Rejected: <span>5</span></div>
    </div>

    <h2>Teacher Statistics</h2>
    <div class="charts-grid">
        <div class="chart-container">
            <canvas id="teacherStatsChart"></canvas>
        </div>
    </div>
</div>

<script>
    var ctx = document.getElementById('teacherStatsChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Verified', 'Pending', 'Rejected'],
            datasets: [{
                label: 'Teacher Status',
                data: [30, 15, 5],
                backgroundColor: ['green', 'yellow', 'red']
            }]
        },
        options: { scales: { y: { beginAtZero: true } } }
    });
</script>
</body>
</html>
