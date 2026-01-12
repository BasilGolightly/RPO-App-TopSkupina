<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['username']) || !isset($_SESSION['role'])) {
    header("Location: login.php");
    exit;
}
include "conn.php";

$id = $_SESSION['user_id'];

$sql = "DELETE FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    exit;
}
$stmt->bind_param("i", $id);
if ($stmt->execute()) {
    header("Location: logout.php");
} else {
    exit("Database error.");
}
