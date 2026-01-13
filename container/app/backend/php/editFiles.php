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

    // file atributi
    $error = "";
    $isBoardPost = $_POST["is_board_post"];
    $allowedCount = $_POST["allowed_count"];
    $maxfileSize = 5 * 1024 * 1024;

    if (!isset($_FILES['bFiles']) || empty($_FILES['bFiles']['name'][0])) {
        $_SESSION["error"] = "No files selected!";
        header("Location: ../../post.php?id=" . $post_id);
        die();
    }

    $fileCount = count($_FILES['bFiles']['name']);


    // upload path - board post ali profile?
    $uploadPath = __DIR__ . "/../../media/";
    if($isBoardPost == "1"){
        $uploadPath = $uploadPath . "board/upload";
    }
    else{
        $uploadPath = $uploadPath . "post/upload";
    }

    // pazi stevilo 
    if($fileCount > $allowedCount){
        $_SESSION["error"] = "Too many files!\n";
        header("Location: ../../post.php?id=" . $post_id);
        die();
    }

    for($i = 0; $i < $fileCount; $i++){
        if($_FILES['bFiles']['size'][$i] <= $maxfileSize){
            if ($_FILES['bFiles']['error'][$i] !== UPLOAD_ERR_OK) {
                $error .= "Upload error for file \"" . $_FILES['bFiles']['name'][$i] . "\"\n";
                continue;
            }
            // get file properties, filename, extension
            $currFile = $_FILES['bFiles']['name'][$i];
            $fileName = "upload";
            $ext = pathinfo($currFile, PATHINFO_EXTENSION);
            $tempPath = $_FILES['bFiles']['tmp_name'][$i];

            // INSERT upload - pridobi id
            $category = "archive";
            $uploadSql = "INSERT INTO upload (id_user, filename, extension, category)
            VALUES (?, ?, ?, ?)";
            $stmtUpload = $conn->prepare($uploadSql);
            $stmtUpload->bind_param("isss", $user_id, $fileName, $ext, $category); 
            $stmtUpload->execute();

            $uploadId = $stmtUpload->insert_id;
            $fileName = $fileName . $uploadId;
            $stmtUpload->close();

            // POSODOBI filepath => "/upload[insert_id].ext"
            $stmtUpdate = $conn->prepare("UPDATE upload SET filename = ? WHERE id = ?");
            $stmtUpdate->bind_param("si", $fileName, $uploadId);
            $currPath = $uploadPath . $uploadId . "." . $ext;

            if(move_uploaded_file($tempPath, $currPath)){
                // INSERT post_upload
                $postUploadSql = "INSERT INTO post_upload (id_post, id_upload) 
                VALUES (?, ?)";
                $stmtPostUp = $conn->prepare($postUploadSql);
                $stmtPostUp->bind_param("ii", $post_id, $uploadId);
                $stmtPostUp->execute();
                $stmtPostUp->close();
            } 
            else{
                $error .= "The file\"" . $currFile = $_FILES['bFiles']['name'][$i] . "\" failed to upload!\n";
                continue;
            }
        }
        // prevlka datoteka 
        else{
            $error .= "The file\"" . $currFile = $_FILES['bFiles']['name'][$i] . "\" is too big (more than 5MB)!\n";
            continue;
        } 
    }

    $_SESSION["error"] = $error;
    header("Location: ../../post.php?id=" . $post_id);
    die();
}

header("Location: ../../index.php");
die();
?>