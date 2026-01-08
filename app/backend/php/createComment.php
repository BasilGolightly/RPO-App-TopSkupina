<?php
include "conn.php";
session_start();
if (!isset($_SESSION["user_id"]) || !isset($_SESSION["username"]) || !isset($_SESSION["role"])) {
    header("Location: ../../index.php");
}

if($_SERVER['REQUEST_METHOD'] == "POST"){
    $user_id = $_SESSION["user_id"];
    $post_id = $_POST["p-Id"];
    $parent_id = "NULL";
    $commentContent = trim($_POST["new-comment"]);

    if(isset($_POST["c-Id"]) && $_POST["c-Id"] != ""){
        $parent_id = $_POST["c-Id"];
        $stmt = $conn->prepare("
        INSERT INTO comment (id_user, id_post, content, id_comment)
        VALUES (?, ?, ?, ?)
        ");
        $stmt->bind_param("iiss", $user_id, $post_id, $commentContent, $parent_id);
    }
    else{
        $stmt = $conn->prepare("
        INSERT INTO comment (id_user, id_post, content, id_comment)
        VALUES (?, ?, ?, NULL)
        ");
        $stmt->bind_param("iis", $user_id, $post_id, $commentContent);
    }    

    /*
    $stmt = $conn->prepare("
        INSERT INTO comment (id_user, id_post, content, id_comment)
        VALUES (?, ?, ?, NULL)
        ");
        $stmt->bind_param("iis", $user_id, $post_id, $commentContent);
    */

    $stmt->execute();
    $stmt->close();
    header("Location: ../../post.php?id=" . $post_id);
}
?>