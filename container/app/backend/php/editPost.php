<?php
include "conn.php";
session_start();
if (!isset($_SESSION["user_id"]) || !isset($_SESSION["username"]) || !isset($_SESSION["role"])) {
    header("Location: ../../index.php");
}

if($_SERVER['REQUEST_METHOD'] == "POST"){
    $user_id = $_SESSION["user_id"];
    $post_id = $_POST["post_id"];
    $title = $_POST["title"];
    $content = $_POST["content"];
    
}

?>