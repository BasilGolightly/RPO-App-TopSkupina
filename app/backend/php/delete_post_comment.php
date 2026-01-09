<?php
include "conn.php";
session_start();
if (!isset($_SESSION["user_id"]) || !isset($_SESSION["username"]) || !isset($_SESSION["role"])) {
    header("Location: ../../index.php");
}

include "conn.php";

$comment_id = (int)($_POST["comment_id"]) ?? 0;
$post_id = (int)($_POST["post_id"]) ?? 0;
$user_id = $_SESSION["user_id"];

$sql = "DELETE FROM comment WHERE id = ? AND id_user = ? AND id_post = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iii", $comment_id, $user_id, $post_id);
$stmt->execute();
$stmt->close();

header("Location: ../../post.php?id=" . $post_id);
exit;
