<?php
// admin_viewteacher.php
include "db_connect.php";

$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$sql = "SELECT id, first_name, last_name, email, role, verified FROM user_details WHERE role='teacher' AND verified='pending'";

if ($filter !== 'all') {
    $sql .= " AND verified='$filter'";
}

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | Teacher List</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
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
        .teacher-list { background-color: white; padding: 20px; border-radius: 5px; box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); }
        .teacher-list h2 { margin-bottom: 10px; }
        .teacher-list input, .teacher-list select { padding: 8px; margin-bottom: 10px; width: 100%; border: 1px solid #ccc; border-radius: 5px; }

        .teacher-list table { width: 100%; border-collapse: collapse; }
        .teacher-list th, .teacher-list td { padding: 10px; border: 1px solid #ddd; text-align: center; }
        .teacher-list th { background-color: #f4f4f4; }

        .teacher-list button { padding: 7px 12px; border-radius: 3px; border: none; margin: 2px; cursor: pointer; }
        .verify { background-color: green; color: white; }
        .reject { background-color: red; color: white; }
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
                <li><a class="active" href="#">Teachers</a></li>
                <li><a href="admin_viewhistory.php">Reports & History</a></li>
                <li><a href="#">Settings</a></li>
            </ul>
        </nav>
        <button class="logout" id="logoutBtn">Log Out</button>
        <script>
            document.getElementById("logoutBtn").addEventListener("click", function() {
                if (confirm("Are you sure you want to log out?")) {
                    window.location.href = "login.php";
                }
            });
        </script>
    </aside>


    <main class="main-content">
        <section class="teacher-list">
            <h2>Teacher List</h2>

            <label for="filter">Filter by Status:</label>
            <select id="filter" onchange="filterTeachers()">
                <option value="all" <?= $filter == 'all' ? 'selected' : '' ?>>All</option>
                <option value="pending" <?= $filter == 'pending' ? 'selected' : '' ?>>Pending</option>
                <option value="verified" <?= $filter == 'verified' ? 'selected' : '' ?>>Verified</option>
                <option value="rejected" <?= $filter == 'rejected' ? 'selected' : '' ?>>Rejected</option>
            </select>

            <input type="text" placeholder="Search by Name or Email..." id="searchInput">

            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr id="user-<?php echo $row['id']; ?>">
                        <td><?php echo $row['first_name'] . " " . $row['last_name']; ?></td>
                        <td><?php echo $row['email']; ?></td>
                        <td><?php echo ucfirst($row['role']); ?></td>
                        <td class="status"><?php echo ucfirst($row['verified']); ?></td>
                        <td class="actions">
                            <?php if ($row['verified'] == 'pending') { ?>
                                <button class="verify" data-id="<?php echo $row['id']; ?>" value="verify">Verify</button>
                                <button class="reject" data-id="<?php echo $row['id']; ?>" value="reject">Reject</button>
                            <?php } else { ?>
                                <span style="color: <?= $row['verified'] == 'verified' ? 'green' : 'red'; ?>;">
                                    <?= ucfirst($row['verified']); ?>
                                </span>
                            <?php } ?>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </section>
    </main>

    <script>
      document.querySelectorAll(".verify, .reject").forEach(button => {
    button.addEventListener("click", function () {
        let id = this.dataset.id;
        let action = this.value;

        if (!confirm(`Are you sure you want to ${action} this user?`)) return;

        fetch("verify_teacher.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: `id=${id}&action=${action}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                let row = document.getElementById(`user-${id}`);
                row.querySelector(".status").textContent = data.status.charAt(0).toUpperCase() + data.status.slice(1);
                row.querySelector(".actions").innerHTML = `<span style="color: ${data.status === 'verified' ? 'green' : 'red'};">${data.message}</span>`;

                // Show alert message
                alert(data.message);
            } else {
                alert(data.message);
            }
        })
        .catch(error => console.error("Error:", error));
    });
});
    </script>
</body>
</html>

<?php $conn->close(); ?>
