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
    //$user_id = $_SESSION['user_id'];
    $rating = $_POST['rating'];
    $userRated = $_POST['alreadyRated'];

    if($rating == 0){
        $error .= "Please rate the post before submitting!\n";
        $_SESSION["error"] = $error;
        header("Location: ../../post.php?id=" . $post_id);
        die();
    } 

    $stmt;
    if($userRated == 1){
        $stmt = $conn -> prepare("
        UPDATE rating 
        SET rating = ?
        WHERE id_post = ? AND id_user = ?
        ");
    }
    else{
        $stmt = $conn -> prepare("
        INSERT INTO rating(rating, id_post, id_user) 
        VALUES (?, ?, ?)
        ");
    }
    
    $stmt -> bind_param("iii", $rating, $post_id, $_SESSION["user_id"]);
    if(!($stmt -> execute())){
        $error .= "Failed to rate post!\n";
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