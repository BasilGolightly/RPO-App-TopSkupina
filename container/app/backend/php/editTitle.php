<?php
include "conn.php";
session_start();
if (!isset($_SESSION["user_id"]) || !isset($_SESSION["username"]) || !isset($_SESSION["role"])) {
    header("Location: ../../index.php");
    die();
}

if($_SERVER['REQUEST_METHOD'] == "POST"){
    $user_id = $_SESSION["user_id"];
    $post_id = $_POST["post_id"];
    $title = trim($_POST["title"]);

    $stmt = $conn -> prepare("
    UPDATE post SET title = ?
    WHERE id = ? AND id_user = ?
    ");    
    $stmt -> bind_param("sii", $title, $post_id, $user_id);
    $stmt -> execute();
    $stmt -> close();
    header("Location: ../../post.php?id=" . $post_id);
    die();
}
header("Location: ../../index.php");
die();
?>