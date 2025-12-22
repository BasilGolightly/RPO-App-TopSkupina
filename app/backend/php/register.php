<?php
include "conn_test.php";

$username   = trim($_POST['username'] ?? '');
$password   = $_POST['password'] ?? '';
$rPassword  = $_POST['repeatPass'] ?? '';

//check
if ($username === '') {
    die('Username is required.');
}

if ($password === '' || $password !== $rPassword) {
    die('Passwords do not match.');
}

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

//conn
$conn = new mysqli("localhost", "root", "", "BitBug");

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
$stmt->execute();

$stmt->close();
$conn->close();

echo "uspeh!";
?>
