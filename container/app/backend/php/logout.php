<?php
session_start();
unset($_SESSION['user_id']);
unset($_SESSION['username']);
unset($_SESSION['role']);
session_destroy();
session_abort();
header("Location: ../../login.php");
?>