<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['username']) || !isset($_SESSION['role'])) {
    header("Location: login.html");
    exit;
}
include "conn.php";

$user_id = $_SESSION["user_id"];
$new_username = trim(string: $_POST["new_username"]);

$min = 3;
$max = 20;

$error = "";

if ($new_username == "") {
    $error = "Username is required";
} elseif (strlen($new_username) < $min || strlen($new_username) > $max) {
    $error = "Username must be 3-20 characters";
}

if ($error) {
    $_SESSION["error"] = $error;
    header("Location: ../../profile.php");
    exit;
}

$sql = "UPDATE users SET username = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $new_username, $user_id);
$stmt->execute();
$_SESSION["username"] = $new_username;

$stmt->close();
$conn->close();
header("Location: ../../profile.php");
exit;
