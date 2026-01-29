<?php

session_start();
require_once "config.php";

$conn->set_charset("utf8");

/**
 * Logs user activity to the database.
 *
 * @param mysqli $conn
 * @param int $userId
 * @param string $activityType
 * @param string $description
 * @return void
 */
function logActivity($conn, $userId, $activityType, $description)
{
    $stmt = $conn->prepare("INSERT INTO activity_logs (user_id, activity_type, description, created_at) VALUES (?, ?, ?, NOW())");
    if ($stmt) {
        $stmt->bind_param("iss", $userId, $activityType, $description);
        $stmt->execute();
        $stmt->close();
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register'])) {

    
    $lastNameInput = isset($_POST['LastName']) ? trim($_POST['LastName']) : '';
    $firstNameInput = isset($_POST['FirstName']) ? trim($_POST['FirstName']) : '';
    $middleNameInput = isset($_POST['MiddleName']) ? trim($_POST['MiddleName']) : '';
    $birthdateInput = isset($_POST['birthdate']) ? trim($_POST['birthdate']) : '';
    $genderInput = isset($_POST['gender']) ? trim($_POST['gender']) : '';
    $phoneInput = isset($_POST['phone']) ? trim($_POST['phone']) : '';
    $emailInput = isset($_POST['email']) ? trim($_POST['email']) : '';
    $passwordInput = isset($_POST['EnterPassword']) ? trim($_POST['EnterPassword']) : '';
    $confirmPasswordInput = isset($_POST['confirmPassword']) ? trim($_POST['confirmPassword']) : '';

    $errors = [];
    $success = false;

    if (
        empty($lastNameInput) || empty($firstNameInput) || empty($middleNameInput) ||
        empty($birthdateInput) || empty($genderInput) || empty($phoneInput) ||
        empty($emailInput) || empty($passwordInput) || empty($confirmPasswordInput)
    ) {
        $errors[] = "All fields are required.";
    }


    if (!empty($lastNameInput) && !preg_match("/^[a-zA-Z\s]+$/", $lastNameInput)) {
        $errors[] = "Last name must contain only letters and spaces.";
    }

    if (!empty($firstNameInput) && !preg_match("/^[a-zA-Z\s]+$/", $firstNameInput)) {
        $errors[] = "First name must contain only letters and spaces.";
    }

    if (!empty($middleNameInput) && !preg_match("/^[a-zA-Z\s]+$/", $middleNameInput)) {
        $errors[] = "Middle name must contain only letters and spaces.";
    }

    if (!empty($emailInput) && !filter_var($emailInput, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    if (!empty($emailInput)) {
        $checkEmailQuery = "SELECT id FROM authentication WHERE email = ?";
        $stmt = $conn->prepare($checkEmailQuery);
        if ($stmt) {
            $stmt->bind_param("s", $emailInput);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $errors[] = "This email is already registered.";
            }
            $stmt->close();
        }
    }

    if (!empty($phoneInput) && !preg_match("/^[0-9]{10}$/", $phoneInput)) {
        $errors[] = "Phone number must be exactly 10 digits.";
    }

    if (!empty($phoneInput)) {
        $checkPhoneQuery = "SELECT id FROM authentication WHERE phone_number = ?";
        $stmt = $conn->prepare($checkPhoneQuery);
        if ($stmt) {
            $stmt->bind_param("s", $phoneInput);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $errors[] = "This phone number is already registered.";
            }
            $stmt->close();
        }
    }

    if (!empty($birthdateInput)) {
        $birthDate = DateTime::createFromFormat('Y-m-d', $birthdateInput);
        if (!$birthDate || $birthDate->format('Y-m-d') !== $birthdateInput) {
            $errors[] = "Invalid birthdate format.";
        } else if ($birthDate > new DateTime()) {
            $errors[] = "Birthdate cannot be in the future.";
        } else {
            // Calculate age
            $now = new DateTime();
            $age = $now->diff($birthDate)->y;
            if ($age < 13) {
                $errors[] = "You must be at least 13 years old to register.";
            }
        }
    }

    if (!empty($genderInput) && !in_array($genderInput, ['male', 'female', 'other'])) {
        $errors[] = "Invalid gender selection.";
    }

    if (!empty($passwordInput) && strlen($passwordInput) < 8) {
        $errors[] = "Password must be at least 8 characters long.";
    }

    if (!empty($passwordInput)) {
        if (!preg_match("/[a-z]/", $passwordInput)) {
            $errors[] = "Password must contain at least one lowercase letter.";
        }
        if (!preg_match("/[A-Z]/", $passwordInput)) {
            $errors[] = "Password must contain at least one uppercase letter.";
        }
        if (!preg_match("/[0-9]/", $passwordInput)) {
            $errors[] = "Password must contain at least one number.";
        }
    }

    if (!empty($passwordInput) && !empty($confirmPasswordInput) && $passwordInput !== $confirmPasswordInput) {
        $errors[] = "Passwords do not match.";
    }

    if (empty($errors)) {
        try {
            
            $hashedPassword = password_hash($passwordInput, PASSWORD_BCRYPT);

            
            $insertQuery = "INSERT INTO authentication 
                            (last_name, first_name, middle_name, birthdate, age, gender, phone_number, email, password_hash, role, created_at, updated_at) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";

            $stmt = $conn->prepare($insertQuery);

            if (!$stmt) {
                throw new Exception("Prepare failed: " . $conn->error);
            }

            
            $birthDateObj = DateTime::createFromFormat('Y-m-d', $birthdateInput);
            $now = new DateTime();
            $age = $now->diff($birthDateObj)->y;

            
            $role = 'user';

            

            $stmt->bind_param(
                "ssssisssss",
                $lastNameInput,
                $firstNameInput,
                $middleNameInput,
                $birthdateInput,
                $age,
                $genderInput,
                $phoneInput,
                $emailInput,
                $hashedPassword,
                $role
            );

            
            if ($stmt->execute()) {
                $success = true;
                $userId = $stmt->insert_id;
                $stmt->close();

                
                logActivity($conn, $userId, 'Registration', 'User registered successfully');

                
                $_SESSION['success_message'] = "Registration successful! Please log in with your email and password.";
                header("Location: index.php?success=1");
                exit();
            } else {
                throw new Exception("Execute failed: " . $stmt->error);
            }
        } catch (Exception $e) {
            $errors[] = "Registration failed: " . $e->getMessage();
        }
    }


    if (!empty($errors)) {
        $_SESSION['registration_errors'] = $errors;
        $_SESSION['form_data'] = [
            'LastName' => $lastNameInput,
            'FirstName' => $firstNameInput,
            'MiddleName' => $middleNameInput,
            'email' => $emailInput
        ];
        header("Location: register.php?error=1");
        exit();
    }
}

require_once 'index.php';

session_start();
require_once "config.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    // 1. Validate input
    if (empty($email) || empty($password)) {
        $_SESSION["login_error"] = "Email and password are required.";
        header("Location: index.php");
        exit();
    }

    // 2. Get user by email (including role)
    $stmt = $conn->prepare(
        "SELECT id, first_name, password_hash, role 
         FROM authentication 
         WHERE email = ?"
    );
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    // 3. Check user exists and verify password
    $loginSuccess = false;
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user["password_hash"])) {
            // 5. Store session
            $_SESSION["user_id"]   = $user["id"];
            $_SESSION["email"]     = $email;
            $_SESSION["role"]      = $user["role"];
            // 6. Redirect based on role
            if ($user["role"] === "admin") {
                header("Location: adminDashboard.php");
            } else {
                header("Location: userDashboard.php");
            }
            exit();
        }
    }
    // Always show a generic error if login fails (wrong email or password)
    $_SESSION["login_error"] = "Invalid email or password.";
    header("Location: index.php");
    exit();
}
