<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['username']) || !isset($_SESSION['role'])) {
    header("Location: ../../login.php");
    exit;
}
include "conn.php";

$post_id = isset($_POST["post_id"]) ? (int)$_POST["post_id"] : 0;
if ($post_id <= 0) {
    header("Location: ../../index.php");
    exit;
}

$user_id = $_SESSION["user_id"];

$sql = "DELETE FROM post WHERE id_user = ? AND id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $user_id, $post_id);
$stmt->execute();

$stmt->close();
$conn->close();

header("Location: ../../index.php");
exit;
