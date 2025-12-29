<?php
session_destroy();
include "conn.php";

$username   = trim($_POST['username'] ?? '');
$password   = $_POST['password'] ?? '';
$rPassword  = $_POST['repeatPass'] ?? '';

//check
if ($username === '') {
    echo "<script>alert('Username is required');</script>";
    die('Username is required.');
}

$unamecheck = "SELECT * FROM users WHERE username = ?";
$stmt_check = $conn->prepare($unamecheck);
$stmt_check->bind_param("s", $username);
$stmt_check->execute();
$unamecheck_result = $stmt_check->get_result();

if($unamecheck_result->num_rows === 1) {
    echo "<script>alert('Name already exists');</script>";
    die("Name already exists");
}
$stmt_check->close();

if ($password === '' || $password !== $rPassword) {
    echo "<script>alert('Password do not match');</script>";
    die("Passwords do not match");
}

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

if ($conn->connect_error) {
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
    die();
}

echo "<script>alert('Napaka pri registraciji');</script>";

$stmt->close();
$conn->close();

?>
