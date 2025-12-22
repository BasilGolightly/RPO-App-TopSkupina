<?php
$sname = "localhost";
$uname = "root";
$password = "";
$db_name = "BitBug";

$conn = new mysqli($sname, $uname, $password, $db_name);

if($conn->connect_error){
    die("Connection failed: " . $conn->connect_error);
}
?>