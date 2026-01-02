<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['username']) || !isset($_SESSION['role'])) {
    header("Location: login.html");
    exit;
}
include "conn.php";

if (!isset($_FILES["pfp"]) || $_FILES["pfp"]["error"] != UPLOAD_ERR_OK) {
    $_SESSION["error"] = "Upload failed";
    header('Location: ../../profile.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$file = $_FILES["pfp"];
$max_file_size = 2 * 1024 * 1024; // 2MB je najvecja velikost datoteke

if ($file["size"] > $max_file_size) {
    $_SESSION["error"] = "File too large";
    header('Location: ../../profile.php');
    exit;
}


// preverjanje file typa
$finfo = new finfo(FILEINFO_MIME_TYPE);
$mime = $finfo->file($file['tmp_name']);

$allowed = [
    "image/jpeg" => "jpg",
    "image/png" => "png",
    "image/webp" => "webp",
];

if (!isset($allowed[$mime])) {
    $_SESSION["error"] = "Invalid file type";
    header('Location: ../../profile.php');
    exit;
}

$ext = $allowed[$mime];
$filename = bin2hex(random_bytes(16)) . '.' . $ext;

$upload_dir = __DIR__ . "/../../media/pfp";

if (!is_dir($upload_dir)) {
    $_SESSION["error"] = "Upload directory missing";
    header('Location: ../../profile.php');
    exit;
}

$dest_path = $upload_dir . "/" . $filename;

if (!move_uploaded_file($file["tmp_name"], $dest_path)) {
    $_SESSION["error"] = "Failed to save file";
    header('Location: ../../profile.php');
    exit;
}

$public_path = 'media/pfp/' . $filename;

function cleanup_and_exit($message, $dest_path)
{
    if (is_file($dest_path)) {
        unlink($dest_path);
    }
    $_SESSION["error"] = $message;
    header('Location: ../../profile.php');
    exit;
}


// sedaj je datoteka, shranjena 

// 1) insert v upload
$sql = "INSERT INTO upload (id_user,filename,extension,category) VALUES (?,?,?,'picture')";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    cleanup_and_exit("Database error", $dest_path);
}
$stmt->bind_param("iss", $user_id, $filename, $ext);
if (!$stmt->execute()) {
    cleanup_and_exit("Database error", $dest_path);
}
$upload_id = $stmt->insert_id;


//2) insert v pfp


// prvo staro zbrisemo
$sql = "DELETE FROM pfp WHERE id_user = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    cleanup_and_exit("Database error", $dest_path);
}
$stmt->bind_param("i", $user_id);
if (!$stmt->execute()) {
    cleanup_and_exit("Database error", $dest_path);
}

// vstavimo novo
$sql = "INSERT INTO pfp (id_user, id_upload) VALUES (?,?)";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    cleanup_and_exit("Database error", $dest_path);
}
$stmt->bind_param("ii", $user_id, $upload_id);
if (!$stmt->execute()) {
    cleanup_and_exit("Database error", $dest_path);
}

header('Location: ../../profile.php');
exit;
