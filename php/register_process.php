    <?php
    //register_process.php
    include "db_connect.php";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Validate required fields
        if (!isset($_POST["email"]) || empty($_POST["email"])) {
            showError(["Error: Email field is required."]);
        }

        $first_name = $_POST["first_name"] ?? '';
        $last_name = $_POST["last_name"] ?? '';
        $username = $_POST["username"] ?? '';
        $department = $_POST["department"] ?? '';
        $employee_id = $_POST["employee_id"] ?? null; // Allow null
        $phone_number = $_POST["phone_number"] ?? '';
        $email = $_POST["email"] ?? '';
        $password = $_POST["password"] ?? '';
        $role = $_POST["role"] ?? 'teacher';

    $verified = ($role === 'teacher') ? 'pending' : 'verified';
        // Hash the password for security
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Check if username, email, phone number, employee_id, first_name, or last_name already exists
        $stmt = $conn->prepare("SELECT * FROM user_details WHERE username = ? OR email = ? OR phone_number = ? OR employee_id = ? OR (first_name = ? AND last_name = ?)");
        $stmt->bind_param("ssssss", $username, $email, $phone_number, $employee_id, $first_name, $last_name);
        $stmt->execute();
        $result = $stmt->get_result();

        $errors = []; // Array to store unique error messages

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                if ($row["username"] === $username && !isset($errors["username"])) {
                    $errors["username"] = "Error: Username is already taken.";
                }
                if ($row["email"] === $email && !isset($errors["email"])) {
                    $errors["email"] = "Error: Email is already in use.";
                }
                if ($row["phone_number"] === $phone_number && !isset($errors["phone_number"])) {
                    $errors["phone_number"] = "Error: Phone number is already registered.";
                }
                if ($row["employee_id"] === $employee_id && !empty($employee_id) && !isset($errors["employee_id"])) {
                    $errors["employee_id"] = "Error: Employee ID is already in use.";
                }
                if ($row["first_name"] === $first_name && $row["last_name"] === $last_name && !isset($errors["name"])) {
                    $errors["name"] = "Error: This name is already registered.";
                }
            }
        }

        // Convert associative array to a simple array
        $errors = array_values($errors);

        // If there are any errors, show them
        if (!empty($errors)) {
            showError($errors);
        }

        // Insert into database
        $stmt = $conn->prepare("INSERT INTO user_details (first_name, last_name, username, department, employee_id, phone_number, email, password, role, verified) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssssss", $first_name, $last_name, $username, $department, $employee_id, $phone_number, $email, $hashed_password, $role, $verified);
        
    if ($stmt->execute()) {
            echo "<div style='text-align: center; font-family: Roboto, sans-serif; margin-top: 50px;'>";
            echo "<h2 style='color: red;'>Registration successful! If you're a teacher, wait for admin approval.</h2>";
            echo "<ul style='color: red; list-style-type: none; padding: 0;'>";
            echo "</ul>";
            echo "<a href='login.php' style='display: inline-block; padding: 10px 20px; background-color: #007bff; color: white; text-decoration: none; border-radius: 5px;'>Log In</a>";
            echo "</div>";
            exit();
        } else {
            showError(["Error: " . $stmt->error]);
        }
    }

    // Function to show all error messages with a back button
    /*function showError($messages) {
        echo "<div style='text-align: center; font-family: Arial, sans-serif; margin-top: 50px;'>";
        echo "<h2 style='color: red;'>There were some errors:</h2>";
        echo "<ul style='color: red; list-style-type: none; padding: 0;'>";
        foreach ($messages as $message) {
            echo "<li>$message</li>";
        }
        echo "</ul>";
        echo "<a href='signup.php' style='display: inline-block; padding: 10px 20px; background-color: #007bff; color: white; text-decoration: none; border-radius: 5px;'>Back to Sign Up</a>";
        echo "</div>";
        exit();
    }*/
    function showError($messages) {
        $errorMessage = implode("\\n", $messages); // Join errors with a newline
        echo "<script>alert('$errorMessage'); history.back();</script>";
        exit();
    }
    $stmt->close(); 
    $conn->close();
    ?>
