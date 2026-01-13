<?php 
session_start();
include "conn.php";

if (!isset($_SESSION['user_id'])) {
    $error = "You must be logged in to create a post.";
    header("Location: ../../login.php?error=" . urlencode($error));
    exit;
}

$u_id = $_SESSION['user_id'];
$title = $_POST['title'] ?? '';
$content = $_POST['content'] ?? '';
$board_id = $_POST['bID'] ?? '';
$board_title = $_POST['bT'] ?? '';

$request_type = $_GET["type"] ?? "";
$id_dis = $_GET["id_dis"] ?? "";
$id_comment = $_GET["id_cmt"] ?? "";

$id = $_GET["id"] ?? "";

if ($request_type == "FollowUser") {
  $sql = "INSERT INTO follow (id_user1, id_user2, accepted)
        VALUES (?, ?, ?)";

  $accepted = 0;

  $stmt = $conn->prepare($sql);
  $stmt->bind_param("iii", $_SESSION['user_id'], $id, $accepted);
  $stmt->execute();

  //$post_id = $stmt->insert_id;

  $stmt->close();

  header("Location: ../../view-profile.php?id=" . urlencode($id) . "&error=" . urlencode($error));
}
elseif ($request_type == "UnfollowUser") {
  $sql = "DELETE FROM follow
        WHERE id_user1 = ?
        AND id_user2 = ?";

  $stmt = $conn->prepare($sql);
  $stmt->bind_param("ii", $_SESSION['user_id'], $id);
  $stmt->execute();

  $stmt->close();

  header("Location: ../../view-profile.php?id=" . urlencode($id));
}


exit;
?>
