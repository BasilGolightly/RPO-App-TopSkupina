<?php
session_start();
include "backend/php/conn.php";

$title = $_GET['title'] ?? '';

if (!$title) {
    echo "No board specified";
    exit;
}

$stmt = $conn->prepare("SELECT * FROM board WHERE title=?");
$stmt->bind_param("s", $title);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Board not found";
    exit;
}

$board = $result->fetch_assoc();

echo "<h1>" . htmlspecialchars($board['title']) . "</h1>";
echo "<p>" . htmlspecialchars($board['description']) . "</p>";
