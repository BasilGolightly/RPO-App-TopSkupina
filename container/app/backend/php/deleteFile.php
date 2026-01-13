<?php
include "conn.php";
session_start();
if (!isset($_SESSION["user_id"]) || !isset($_SESSION["username"]) || !isset($_SESSION["role"])) {
    header("Location: ../../index.php");
    die();
}

$error = "";

if($_SERVER['REQUEST_METHOD'] == "POST"){
    $post_id = $_POST['pId'];
    $upload_id = $_POST['uId'];
    $ext = $_POST['uExt'];
    $isBoardPost = $_POST['is_board_post'];

    $path = __DIR__ . "/../../media/";
    if($isBoardPost == 1){ $path = $path . "board/"; }
    else { $path = $path . "post/"; }
    $path = $path . "upload" . $upload_id . "." . $ext;

    if(is_file($path)){
        if(!unlink($path)){
            $error .= "Failed to delete file!\n";
        }
    }

    $stmt = $conn -> prepare("
    DELETE FROM upload
    WHERE id = ?
    ");
    $stmt -> bind_param("i", $upload_id);
    if(!$stmt -> execute()){
        $error .= "Failed to remove upload from database!\n";
    }

    $stmt -> close();
    $_SESSION["error"] = $error;
    header("Location: ../../post.php?id=" . $post_id);
    die();
}

$_SESSION["error"] = $error;
header("Location: ../../index.php");
die();
?>