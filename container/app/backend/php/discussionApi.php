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

if ($request_type == "NewDis") {
  $sql = "INSERT INTO discussion (title, content, id_board, id_user)
        VALUES (?, ?, ?, ?)";

  $stmt = $conn->prepare($sql);
  $stmt->bind_param("sssi", $title, $content, $board_id, $u_id);
  $stmt->execute();

  $post_id = $stmt->insert_id;

  $stmt->close();

  header("Location: ../../discussion.php?id=" . urlencode($post_id) . "&error=" . urlencode($error));
}
elseif ($request_type == "NewComment") {
  $sql = "INSERT INTO user_discussion (id_user, id_discussion, content)
        VALUES (?, ?, ?)";

  $stmt = $conn->prepare($sql);
  $stmt->bind_param("iis", $u_id, $id_dis, $content );
  $stmt->execute();

  $post_id = $stmt->insert_id;

  $stmt->close();

  header("Location: ../../discussion.php?id=" . urlencode($id_dis) . "&error=" . urlencode($error));
}
elseif ($request_type == "DeleteComment") {
  $sql = "DELETE FROM user_discussion WHERE id = ? AND id_user = ?";

  $stmt = $conn->prepare($sql);
  $stmt->bind_param("ii", $id_comment, $u_id);
  $stmt->execute();

  $stmt->close();

  header("Location: ../../discussion.php?id=" . urlencode($id_dis) . "&error=" . urlencode($error));
}
elseif ($request_type == "DeleteDiscussion") {
  $sql = "DELETE FROM discussion WHERE id = ? AND id_user = ?";

  $stmt = $conn->prepare($sql);
  $stmt->bind_param("ii", $id_dis, $u_id);
  $stmt->execute();

  $stmt->close();

  header("Location: ../../board.php?title=" . urlencode($board_title) . "&error=" . urlencode($error));
}


exit;
?>
