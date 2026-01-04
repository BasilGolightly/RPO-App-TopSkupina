<?php 
session_start();
include "conn.php";


if (!isset($_SESSION['user_id'])) {
    $error = "You must be logged in to create a post.";
    header("Location: ../../login.php?error=" . urlencode($error));
    exit;
}


$u_id = $_SESSION['user_id'];
$title = $_POST['title'] ?? '';
$content = $_POST['content'] ?? '';
$board_title = $_GET['title'];  

// tole ne rabim pomoje
$stmt_board = $conn->prepare("SELECT id FROM board WHERE title = ?");
$stmt_board->bind_param("s", $board_title);
$stmt_board->execute();
$result_board = $stmt_board->get_result();

if ($result_board->num_rows === 0) {
    $error = "Board not found.";
    header("Location: ../../boards.php?error=" . urlencode($error));
    exit;
}

$board = $result_board->fetch_assoc();
$board_id = $board['id'];  
$stmt_board->close();


$sql = "INSERT INTO post (title, content, id_user)
        VALUES (?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ssi", $title, $content, $u_id);
$stmt->execute();

$post_id = $stmt->insert_id;
$stmt->close();


$sql_relation = "INSERT INTO board_post (id_board, id_post) VALUES (?, ?)";
$stmt_relation = $conn->prepare($sql_relation);
$stmt_relation->bind_param("ii", $board_id, $post_id);
$stmt_relation->execute();
$stmt_relation->close();

header("Location: ../../board.php?title=" . urlencode($board_title));
exit;
?>
