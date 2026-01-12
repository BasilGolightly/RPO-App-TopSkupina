<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['username']) || !isset($_SESSION['role'])) {
    header("Location: login.html");
    exit;
}
include "conn.php";

$user_id = $_SESSION["user_id"];
$new_password = trim(string: $_POST["new_password"]);

$min = 3;
$max = 20;

$error = "";

if ($new_password == "") {
    $error = "Password is required";
} else if (strlen($new_password) < $min || strlen($new_password) > $max) {
    $error = "Password must be 3-20 characters";
}

if ($error) {
    $_SESSION["error"] = $error;
    header("Location: ../../profile.php");
    exit;
}

$hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

$sql = "UPDATE users SET password = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $hashed_password, $user_id);
$stmt->execute();

$stmt->close();
$conn->close();
header("Location: ../../profile.php");
exit;
