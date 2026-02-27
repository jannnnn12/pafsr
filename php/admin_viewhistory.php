<?php
// admin_viewhistory.php
include "db_connect.php";

$sql = "SELECT first_name, last_name, email, action, action_date FROM teacher_verification_history ORDER BY action_date DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | Verification History</title>
    <link rel="icon" type="image/x-icon" href="../img/logo1.png">
    <style>
       * { box-sizing: border-box; margin: 0; padding: 0; font-family: Arial, sans-serif; }
        body { display: flex; height: 100vh; background-color: #f0f0f0; }

    
        .sidebar { width: 250px; background-color: #222; color: white; padding: 20px; position: relative; }
        .sidebar .logo { font-size: 24px; font-weight: bold; text-align: center; margin-bottom: 20px; }
        
        
        .admin-profile {
            background-color: #333;color: white;padding: 10px;border-radius: 5px;margin-bottom: 20px;text-align: center;}

        .admin-profile span {font-weight: bold;}

        .sidebar nav ul { list-style: none; padding: 0; }
        .sidebar nav ul li { margin: 10px 0; }
        .sidebar nav ul li a { display: block; color: white; text-decoration: none; padding: 10px; border-radius: 5px; }
        .sidebar nav ul li a:hover, .sidebar nav ul li.active a { background-color: #555; }
        .logout { background-color: red; padding: 10px; text-align: center; border-radius: 5px; cursor: pointer; width: 100%; }
        .main-content { flex: 1; padding: 20px; }
        .history-list { background-color: white; padding: 20px; border-radius: 5px; box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); }
        .history-list table { width: 100%; border-collapse: collapse; }
        .history-list th, .history-list td { padding: 10px; border: 1px solid #ddd; text-align: center; }
        .history-list th { background-color: #f4f4f4; }
    </style>
</head>
<body>
    <aside class="sidebar">
    <div class="admin-profile">
            <span>Hi, Admin</span>
        </div>
        <div class="logo">RetentionX</div>
        <nav>
            <ul>
                <li><a href="admin_viewteacher.php">Teachers</a></li>
                <li><a class="active" href="#">Reports & History</a></li>
                <li><a href="#">Settings</a></li>
            </ul>
        </nav>
        <button class="logout" id="logoutBtn">Log Out</button>
    </aside>

    <main class="main-content">
        <section class="history-list">
            <h2>Verification History</h2>
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Action</th>
                        <th>Action Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo $row['first_name'] . " " . $row['last_name']; ?></td>
                            <td><?php echo $row['email']; ?></td>
                            <td><?php echo ucfirst($row['action']); ?></td>
                            <td><?php echo $row['action_date']; ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </section>
    </main>
</body>
</html>

<?php $conn->close(); ?>
