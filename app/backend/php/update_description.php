<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['username']) || !isset($_SESSION['role'])) {
    header("Location: login.php");
    exit;
}
include "conn.php";

$user_id = $_SESSION["user_id"];
$description = trim($_POST["description"] ?? "");

$sql = "UPDATE users SET description = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $description, $user_id);
$stmt->execute();

$stmt->close();
$conn->close();

header("Location: ../../profile.php");
exit;
