<?php
session_start();
session_destroy();
include "conn.php";
unset($_SESSION['registerError']);

$username   = trim($_POST['username'] ?? '');
$password   = $_POST['password'] ?? '';
$rPassword  = $_POST['repeatPass'] ?? '';

//check
if ($username === '') {
    $_SESSION['registerError'] = 'Username is required.';
    header("Location: ../../login.html");
    die('Username is required.');
}

$unamecheck = "SELECT * FROM users WHERE username = ?";
$stmt_check = $conn->prepare($unamecheck);
$stmt_check->bind_param("s", $username);
$stmt_check->execute();
$unamecheck_result = $stmt_check->get_result();

if($unamecheck_result->num_rows === 1) {
    $_SESSION['registerError'] = "Name already exists.";
    header("Location: ../../register.php");
    die("Name already exists.");
}
$stmt_check->close();

if ($password === '' || $password !== $rPassword) {
    $_SESSION['registerError'] = "Password do not match";
    header("Location: ../../register.php");
    die("Passwords do not match");
}

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

if ($conn->connect_error) {
    $_SESSION['registerError'] = "Connection failed";
    header("Location: ../../register.php");
    die("Connection failed: " . $conn->connect_error);
}

//sql stavek
$sql = "INSERT INTO users (username, password, role, joined)
        VALUES (?, ?, 'user', CURDATE())";

$stmt = $conn->prepare($sql);

//dodamo parametre na ?
$stmt->bind_param("ss", $username, $hashedPassword);

//izvedi
if($stmt->execute()){
    session_start();
    $_SESSION["user_id"] = $stmt->insert_id;
    $_SESSION["username"] = $username;
    $_SESSION["role"] = "user";
    $stmt->close();
    $conn->close();
    header('Location: ../../index.php');
}
else{
    $_SESSION['registerError'] = "Failed registration.";
    $stmt->close();
    $conn->close();
    header("Location: ../../register.php");
    die("Failed registration.");
}


?>
