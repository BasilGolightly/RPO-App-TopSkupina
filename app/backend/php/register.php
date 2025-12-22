<?php
include "conn.php";

$username   = trim($_POST['username'] ?? '');
$password   = $_POST['password'] ?? '';
$rPassword  = $_POST['repeatPass'] ?? '';

//check
if ($username === '') {
    die('Username is required.');
}

$unamecheck = "SELECT * FROM users WHERE username = ?";
$stmt_check = $conn->prepare($unamecheck);
$stmt_check->bind_param("s", $username);
$stmt_check->execute();
$unamecheck_result = $stmt_check->get_result();

if($unamecheck_result->num_rows === 1) {
    die('Name already exists.');
}
$stmt_check->close();

if ($password === '' || $password !== $rPassword) {
    die('Passwords do not match.');
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
$stmt->execute();

$stmt->close();
$conn->close();

echo "uspeh!";
?>
