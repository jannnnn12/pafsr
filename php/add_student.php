<?php
session_start();
if (!isset($_SESSION['user_id'])) {  
    header("Location: login.php");
    exit();
}
include "db_connect.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="../img/logo1.png">
    <title>Add Student</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }

        body {
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .form-container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            width: 400px;
        }

        h2 {
            text-align: center;
            margin-bottom: 15px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }

        input, select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .btn-container {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
        }

        button {
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        .submit-btn {
            background-color: #007bff;
            color: white;
            width: 100%;
        }

        .submit-btn:hover {
            background-color: #0056b3;
        }

        .back-btn {
            background-color: #dc3545;
            color: white;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            width: 100%;
            padding: 10px;
            border-radius: 5px;
            margin-top: 10px;
            text-align: center;
        }

        .back-btn:hover {
            background-color: #c82333;
        }

    </style>
</head>
<body>

<div class="form-container">
    <h2>Add Student</h2>
    <form action="add_student_process.php" method="POST">
        <div class="form-group">
            <label for="student_id">Student ID:</label>
            <input type="text" id="student_id" name="student_id" required pattern="[A-Za-z0-9]+" title="Only letters and numbers allowed" maxlength="10">
         </div>
        
        <div class="form-group">
            <label for="first_name">First Name:</label>
            <input type="text" id="first_name" name="first_name" required>
        </div>

        <div class="form-group">
            <label for="last_name">Last Name:</label>
            <input type="text" id="last_name" name="last_name" required>
        </div>

        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
        </div>

        <div class="form-group">
            <label for="course">Course:</label>
            <input type="text" id="course" name="course" required>
        </div>

        <div class="form-group">
            <label for="year_level">Year Level:</label>
            <select id="year_level" name="year_level" required>
                <option value="1">1st Year</option>
                <option value="2">2nd Year</option>
                <option value="3">3rd Year</option>
                <option value="4">4th Year</option>
            </select>
        </div>

        <div class="form-group">
            <label for="section">Section:</label>
            <input type="text" id="section" name="section">
        </div>

        <div class="form-group">
            <label for="status">Status:</label>
            <select id="status" name="status">
                <option value="Active">Active</option>
                <option value="Dropped">Dropped</option>
                <option value="Inactive">Inactive</option>
            </select>
        </div>

        <button type="submit" class="submit-btn">Add Student</button>
    </form>

    <a href="viewstudent.php" class="back-btn">Back to View Students</a>
</div>

</body>
</html>
