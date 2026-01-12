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
$board_id = $_POST['bID'] ?? '';
$board_title = $_POST['bT'] ?? '';
//$board_title = $_GET['title'];

// files
$maxfileSize = 5 * 1024 * 1024;
$maxfileCount = 5;
$fileCount = count($_FILES['bFiles']['name']);
$uploadPath = __DIR__ . "/../../media/board/upload";

/* tole ne rabim pomoje (res je)
$stmt_board = $conn->prepare("SELECT id FROM board WHERE title = ?");
$stmt_board->bind_param("s", $board_title);
$stmt_board->execute();
$result_board = $stmt_board->get_result();

if ($result_board->num_rows === 0) {
    $error = "Board not found.";
    header("Location: ../../boards.php?error=" . urlencode($error));
    exit;
}*/

$sql = "INSERT INTO post (title, content, id_user)
        VALUES (?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ssi", $title, $content, $u_id);
$stmt->execute();

$post_id = $stmt->insert_id;

$stmt->close();

// board_post

$sql_relation = "INSERT INTO board_post (id_board, id_post) VALUES (?, ?)";
$stmt_relation = $conn->prepare($sql_relation);
$stmt_relation->bind_param("ii", $board_id, $post_id);
$stmt_relation->execute();
$stmt_relation->close();

// file upload
if($fileCount <= $maxfileCount){
    for($i = 0; $i < $fileCount; $i++){
        if($_FILES['bFiles']['size'][$i] <= $maxfileSize){
            // get file properties, filename, extension
            $currFile = $_FILES['bFiles']['name'][$i];
            //$fileName = pathinfo($currFile, PATHINFO_FILENANE);
            $fileName = "upload";
            $ext = pathinfo($currFile, PATHINFO_EXTENSION);
            $tempPath = $_FILES['bFiles']['tmp_name'][$i];

            // INSERT upload
            $category = "archive";
            $uploadSql = "INSERT INTO upload (id_user, filename, extension, category)
            VALUES (?, ?, ?, ?)";
            $stmtUpload = $conn->prepare($uploadSql);
            $stmtUpload->bind_param("isss", $u_id, $fileName, $ext, $category); 
            $stmtUpload->execute();

            $uploadId = $stmtUpload->insert_id;
            $fileName = $fileName . $uploadId;
            $stmtUpload->close();

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
                $error .= "The file\"" . $currFile = $_FILES['bFiles']['name'][$i] . "\" failed to upload!";
                continue;
            }
        }
        // too big of a file!
        else{
            $error = "The file\"" . $currFile = $_FILES['bFiles']['name'][$i] . "\" is too big (more than 5MB)!";
            continue;
        } 
    }
}
else{
    $error = "Too many (" . $fileCount . ") files!";
}

header("Location: ../../board.php?id=" . urlencode($board_id) . "&error=" . urlencode($error));
exit;
?>
