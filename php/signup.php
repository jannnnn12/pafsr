<?php
include "db_connect.php";
 //signup.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Create Account</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&family=Open+Sans:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="../img/logo1.png">
    <style>

        body{
            opacity: 0;
            animation: fadeIn 1s ease-in-out forwards;
        }
        @keyframes fadeIn {
            to {
                opacity: 1;
            }
        }
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f7fafc;
            margin: 0;
            display: flex;
            min-height: 100vh;
        }
        h1, h2, h3 {
            font-family: 'Open Sans', sans-serif;
        }
        .left-image {
            display: none;
            width: 60%;
            height: 100vh;
        }
        .left-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .right-form {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40%;
            background-color: black;
            color: white;
            padding: 2rem;
        }
        .form-container {
            max-width: 350px;
            width: 100%;
        }
        h2 {
            text-align: center;
            font-size: 2rem;
            font-weight: bold;
            margin: 1rem 0;
        }
        p {
            text-align: center;
            margin-top: 0.5rem;
            color: #a0aec0;
        }
        
        form {
            margin-top: 2rem;
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }
        @keyframes slideIn {
        to {
            opacity: 1;
            transform: translateY(0);
            }
            }
        .form-group {
            display: flex;
            flex-direction: column;
        }
        .name-container {
            display: flex;     
            gap: 1rem;
            
        }
        .name-container .form-group {
            flex: 1;
            width: 0px;
        }
        .form-group label {
            margin-bottom: 0.5rem;
            font-size: 0.875rem;
            color: #a0aec0;
        }
        button, .blue-link {
            padding: 0.75rem;
            background-color: transparent;
            color: #007bff;
            border: none;
            cursor: pointer;
            font-size: 1rem;
            text-decoration: none;
            transition: color 0.3s ease-in-out;
        }
        button:hover, .blue-link:hover {
            text-decoration: underline;
        }
        button:active {
            transform: scale(0.95);
        }
        .form-group input,
        .form-group select {
            padding: 0.5rem;
            border: 1px solid #555;
            border-radius: 0.25rem;
            background-color: #222;
            color: white;
            transition: all 0.3s ease;
        }
        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #007bff;
            transform: scale(1.05);
        }
        .terms {
            display: flex;
            align-items: center;
        }
        .terms input {
            margin-right: 0.5rem;
        }
        .terms a {
            color: #007bff;
            text-decoration: none;
        }
        .terms a:hover {
            text-decoration: underline;
        }
        button {
            padding: 0.75rem;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 0.5rem;
            cursor: pointer;
            font-size: 1rem;
            transition: background-color 0.3s ease-in-out, transform 0.2s;
            text-decoration: none;
        }
        button:hover {
            background-color: #0056b3;
            transform: scale(1.05);
        }
        button:active {
            transform: scale(0.95);
        }
        @media (min-width: 768px) {
            .left-image {
                display: block;
            }
        }
    </style>
</head>
<body>
    <div class="left-image">
        <img src="../img/bg.jpg" alt="Golden High School building with a clear sky in the background" width="800" height="1000"/>
    </div>
    <div class="right-form">
        <div class="form-container">
            <h2>Create An Account</h2>
            <p>Already a member? <a href="login.php" class="blue-link">Sign in</a></p>
            <form action="register_process.php" method="POST">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" placeholder="Enter Username" required />
                </div>
                <div class="name-container">
                    <div class="form-group">
                        <label for="first_name">First name</label>
                        <input type="text" id="first_name" name="first_name" placeholder="Enter first name" required />
                    </div>
                    <div class="form-group">
                        <label for="last_name">Last name</label>
                        <input type="text" id="last_name" name="last_name" placeholder="Enter last name" required />
                    </div>
                </div>
                <div class="form-group">
                    <label for="employee_id">Employee ID (Optional)</label>
                    <input type="text" id="employee_id" name="employee_id" placeholder="Employee ID (Optional)" />
                </div>
                <div class="form-group">
                    <label for="department">Department</label>
                    <select id="department" name="department" required>
                        <option value="" disabled selected>Select your department</option>
                        <option value="Information technology">BSIT</option>
                        <option value="Education">BSED</option>
                        <option value="Tourism">BSTM</option>
                        <option value="Hospital Management">BSHM</option>
                        <option value="Criminology">BSCRIM</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="phone_number">Phone Number</label>
                    <input type="tel" id="phone_number" name="phone_number" placeholder="09XXXXXXXXX" required maxlength="11" pattern="09[0-9]{9}" title="Enter a valid 11-digit phone number (e.g., 09123456789)"/>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="Enter your email" required />
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Create a password (Minimum of 8 characters)" required />
                </div>
                <div class="form-group">
                    <label for="confirm-password">Confirm Password</label>
                    <input type="password" id="confirm-password" name="confirm-password" placeholder="Confirm password" required />
                </div>
                <div class="terms">
                    <input type="checkbox" id="terms" name="terms" required />
                    <label for="terms">Agree with <a href="terms_conditions" class="blue-link">terms & conditions</a></label>
                </div>
                <button type="submit">Create Account</button>
            </form>
        </div>
    </div>

    <script>
        
        document.addEventListener("DOMContentLoaded", function () {
            const phoneNumberInput = document.getElementById("phone_number");
            
    
            const passwordInput = document.getElementById("password");
            const confirmPasswordInput = document.getElementById("confirm-password");

            passwordInput.addEventListener("input", function () {
                passwordInput.style.borderColor = passwordInput.value.length >= 8 ? "green" : "red";
            });

            confirmPasswordInput.addEventListener("input", function () {
                confirmPasswordInput.style.borderColor = confirmPasswordInput.value === passwordInput.value ? "green" : "red";
            });
        });
    </script>
</body>
</html>
