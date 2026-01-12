<?php
session_start();
include "conn.php";

if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    exit;
}

$boardId = (int)($_GET['id'] ?? 0);
$userId  = $_SESSION['user_id'];

if (!$boardId) {
    die("Invalid board");
}

$stmt = $conn->prepare("
    SELECT id FROM board
    WHERE id = ? AND id_user = ?
");
$stmt->bind_param("ii", $boardId, $userId);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    $stmt->close();
    http_response_code(403);
    exit("Not allowed");
}
$stmt->close();

$conn->query("DELETE FROM board_follow WHERE id_board = $boardId");
$conn->query("DELETE FROM board_tag WHERE id_board = $boardId");
$conn->query("DELETE FROM board_post WHERE id_board = $boardId");

$stmt = $conn->prepare("DELETE FROM board WHERE id = ?");
$stmt->bind_param("i", $boardId);
$stmt->execute();
$stmt->close();

header("Location: ../../boards.php");
exit;
