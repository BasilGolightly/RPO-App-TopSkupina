<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['username']) || !isset($_SESSION['role'])) {
    header("Location: login.html");
    exit;
}
include "conn.php";

$id = $_SESSION['user_id'];
$privacyOption = $_POST['visibility'];

$sql = "UPDATE users SET privacy = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
if(!$stmt){
    $_SESSION["error"] = "Failed to update user settings. Try again.";
    header("Location: ../../profile.php");
    die("Failed to update user settings. Try again.");
}
$stmt->bind_param("si", $privacyOption, $id);

if($stmt->execute()){
    header("Location: ../../profile.php");
}
else{
    $_SESSION["error"] = "Failed to update user settings. Try again.";
    header("Location: ../../profile.php");
    die("Failed to update user settings. Try again.");
}
?>