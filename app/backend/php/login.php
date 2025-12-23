<?php
session_start();

include "conn.php";

$username   = trim($_POST['username'] ?? '');
$password   = $_POST['password'] ?? '';

//username check
if($username == ""){
    echo "<script>alert('Username is required');</script>";
    die('Username is required.');
}

//password check
if($password == ""){
    echo "<script>alert('Password is required');</script>";
    die('Password is required.');
}

$sql = "SELECT id, username, password, role FROM users WHERE username =?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();

$result = $stmt->get_result();

//ni usernama
if($result->num_rows !== 1){
    echo "<script>alert('Username not found');</script>";
    die("Username not found");
}

$row = $result->fetch_assoc();

//nepravilen password
if(!password_verify($password, $row["password"])){
    echo "<script>alert('Wrong password');</script>";
    die("Wrong password");
}

//seja
$_SESSION["user_id"] = $row["id"];
$_SESSION["username"] = $row["username"];
$_SESSION["role"] = $row["role"];

$stmt->close();
$conn->close();

header("Location: ../../index.php");
exit;
?>
