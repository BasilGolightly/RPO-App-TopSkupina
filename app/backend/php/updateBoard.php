<?php
session_start();
include "conn.php";

if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    exit;
}

$boardId = (int)$_POST['board_id'];
$title = trim($_POST['title']);
$desc = trim($_POST['description']);
$tagsRaw = $_POST['tags'];
$tagsArray = array_map('trim', explode(',', $tagsRaw));
$tagsArray = array_filter($tagsArray, fn($tag) => $tag !== '');
$tagsArray = array_map('strtolower', $tagsArray);
$tagsArray = array_unique($tagsArray);

$stmt = $conn->prepare("
    UPDATE board
    SET title = ?, description = ?
    WHERE id = ? AND id_user = ?
");
$stmt->bind_param("ssii", $title, $desc, $boardId, $_SESSION['user_id']);
$stmt->execute();
$stmt->close();

if (!empty($tagsArray)) {
    $conn->query("DELETE FROM board_tag WHERE id_board = $boardId");
    $insertTag = $conn->prepare("INSERT IGNORE INTO tag (name) VALUES (?)");
    $getTagId  = $conn->prepare("SELECT id FROM tag WHERE name=?");
    $linkTag   = $conn->prepare(
        "INSERT IGNORE INTO board_tag (id_board, id_tag) VALUES (?, ?)"
    );

    foreach ($tagsArray as $tag) {
        $insertTag->bind_param("s", $tag);
        $insertTag->execute();

        $getTagId->bind_param("s", $tag);
        $getTagId->execute();
        $tagId = $getTagId->get_result()->fetch_assoc()['id'];

        $linkTag->bind_param("ii", $boardId, $tagId);
        $linkTag->execute();
    }

    $insertTag->close();
    $getTagId->close();
    $linkTag->close();
}else{
    $conn->query("DELETE FROM board_tag WHERE id_board = $boardId");
}


header("Location: ../../board.php?id=" . urlencode($boardId));
exit;
