<?php
/* ZA DOCKER
$sname = getenv("DB_HOST");
$uname = getenv("DB_USER");
$password = getenv("DB_PASSWORD");
$db_name = getenv("DB_NAME");
*/
$sname = "localhost";
$uname = "root";
$password = "";
$db_name = "BitBug";

$conn = new mysqli($sname, $uname, $password, $db_name);

if($conn->connect_error){
    die("Connection failed: " . $conn->connect_error);
}
?>