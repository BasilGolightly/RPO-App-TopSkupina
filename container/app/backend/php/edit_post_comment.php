<?php
include "conn.php";
session_start();

if (!isset($_SESSION["user_id"]) || !isset($_SESSION["username"]) || !isset($_SESSION["role"])) {
    header("Location: ../../index.php");
}


$user_id = $_SESSION["user_id"];
$post_id = (int)$_POST["post_id"] ?? 0;
$comment_id = (int)$_POST["comment_id"] ?? 0;
$new_comment = $_POST["new_comment"] ?? "";

$sql = "UPDATE comment
        SET content = ?
        WHERE id = ? AND id_post = ? AND id_user = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("siii", $new_comment, $comment_id, $post_id, $user_id);
$stmt->execute();
$stmt->close();
$conn->close();


header("Location: ../../post.php?id=" . $post_id);
exit;
