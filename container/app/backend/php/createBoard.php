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
$tagsRaw = $_POST['tags'] ?? '';
$tagsRaw = $_POST['tags'] ?? '';
$tagsArray = array_map('trim', explode(',', $tagsRaw));
$tagsArray = array_filter($tagsArray, fn($tag) => $tag !== '');
$tagsArray = array_map('strtolower', $tagsArray);
$tagsArray = array_unique($tagsArray);

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

$boardId = $conn->insert_id;
$stmt->close();

//tags
if (!empty($tagsArray)) {
    //stavki
    $insertTag = $conn->prepare("INSERT IGNORE INTO tag (name) VALUES (?)");
    $getTagId = $conn->prepare("SELECT id FROM tag WHERE name=?");
    $linkTag = $conn->prepare("INSERT IGNORE INTO board_tag (id_board, id_tag) VALUES (?, ?)");

    foreach ($tagsArray as $tag) {
        //vstavi tag (ce ze obstaja ignorira)
        $insertTag->bind_param("s", $tag);
        $insertTag->execute();

        //tag id
        $getTagId->bind_param("s", $tag);
        $getTagId->execute();
        $tagId = $getTagId->get_result()->fetch_assoc()['id'];

        //povezemo board in tag
        $linkTag->bind_param("ii", $boardId, $tagId);
        $linkTag->execute();
    }

    //zapremo
    $insertTag->close();
    $getTagId->close();
    $linkTag->close();
}

header("Location: ../../board.php?id=" . urlencode($boardId));
exit;
?>