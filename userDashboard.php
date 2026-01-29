<?php
session_start();
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "user") {
    header("Location: index.php");
    exit();
}
require_once "config.php";

$userId = $_SESSION["user_id"];
$query = "SELECT last_name, first_name, middle_name, birthdate, age, gender, phone_number, email, created_at FROM authentication WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="CSS/userDashboard.css">
    
</head>
<body>

<div class="profile-card">
    <button style="float:right;" onclick="window.location.href='index.php'">Logout</button>
    <h2>User Profile</h2>
    <div class="avatar">
        <span style="font-size:2em;"><?php echo strtoupper(substr($user['first_name'],0,1)); ?></span>
    </div>
    <h3><?php echo strtoupper($user['first_name']); ?></h3>
    <p>Email Address<br><?php echo htmlspecialchars($user['email']); ?></p>
    <p>Full Name<br><?php echo htmlspecialchars($user['last_name'] . ', ' . $user['first_name'] . ' ' . $user['middle_name']); ?></p>
    <p>Birthdate<br><?php echo htmlspecialchars($user['birthdate']); ?></p>
    <p>Age<br><?php echo htmlspecialchars($user['age']); ?></p>
    <p>Gender<br><?php echo htmlspecialchars(ucfirst($user['gender'])); ?></p>
    <p>Phone<br>+63<?php echo htmlspecialchars($user['phone_number']); ?></p>
</div>
</body>
</html>