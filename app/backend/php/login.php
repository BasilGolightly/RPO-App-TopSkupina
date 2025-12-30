<?php
session_start();
include "conn.php";
unset($_SESSION['loginError']);

$username   = trim($_POST['username'] ?? '');
$password   = trim($_POST['password'] ?? '');

//username check
if($username == ""){
    $_SESSION['loginError'] = "Username is required.";
    header("Location: ../../login.php");
    die('Username is required.');
}

//password check
if($password == ""){
    $_SESSION['loginError'] = "Password is required.";
    die('Password is required.');
    header("Location: ../../login.php");
}

$sql = "SELECT id, username, password, role FROM users WHERE username =?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();

$result = $stmt->get_result();

//ni usernama
if($result->num_rows !== 1){
    $_SESSION['loginError'] = "Username not found.";
    header("Location: ../../login.php");
    die("Username not found");
}

$row = $result->fetch_assoc();

//nepravilen password
if(!password_verify($password, $row["password"])){
    $_SESSION['loginError'] = "Wrong password. Try again.";
    header("Location: ../../login.php");
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
