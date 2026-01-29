<?php
session_start();

require_once "config.php";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["user_id"]) && isset($_POST["role"])) {
    $userId = intval($_POST["user_id"]);
    $role = $_POST["role"] === "admin" ? "admin" : "user";

    $stmt = $conn->prepare("UPDATE authentication SET role = ? WHERE id = ?");
    $stmt ->bind_param("si", $role, $userId);
    $stmt ->execute();
    $stmt ->close();
}

header("Location : adminDashboard.php");
exit();