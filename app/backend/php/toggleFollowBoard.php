<?php
session_start();
include "conn.php";

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    exit;
}

$userId  = $_SESSION['user_id'];
$boardId = $_POST['board_id'];
$url = $_POST['url'] ?? 'Index.php';

$stmt = $conn->prepare(
    "SELECT 1 FROM board_follow WHERE id_user=? AND id_board=?"
);
$stmt->bind_param("ii", $userId, $boardId);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    // Unfollow
    $stmt->close();
    $stmt = $conn->prepare(
        "DELETE FROM board_follow WHERE id_user=? AND id_board=?"
    );
} else {
    // Follow
    $stmt->close();
    $stmt = $conn->prepare(
        "INSERT INTO board_follow (id_user, id_board) VALUES (?, ?)"
    );
}

$stmt->bind_param("ii", $userId, $boardId);
$stmt->execute();
$stmt->close();

header("Location: $url");
exit;