<?php
session_start();
//session_destroy();
include "conn.php";
unset($_SESSION['registerError']);

if (!isset($_SESSION['user_id'])) {
    $error = "You must be logged in to create a board.";
    header("Location: ../../login.php?error=" . urlencode($error));
    exit;
}

$u_id = $_SESSION['user_id'];
$title = $_POST['title'] ?? '';
$tags = $_POST['tags'] ?? null;
$description  = $_POST['description'] ?? null;

$titlecheck = "SELECT * FROM board WHERE title = ?";
$stmt_check = $conn->prepare($titlecheck);
$stmt_check->bind_param("s", $title);
$stmt_check->execute();
$titlecheck_result = $stmt_check->get_result();

if($titlecheck_result->num_rows > 0) {
    $error = "Board already exists.";
    header("Location: ../../boards.php?error=" . urlencode($error));
    exit;
}
$stmt_check->close();

$sql = "INSERT INTO board (title, id_user, description)
        VALUES (?, ?, ?)";

$stmt = $conn->prepare($sql);

$stmt->bind_param("sss", $title, $u_id, $description);

$stmt->execute();
$stmt->close();

header("Location: ../../board.php?title=" . urlencode($title));
exit;
?>