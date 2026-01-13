<?php
session_start();
include "backend/php/conn.php";

$id_discussion = $_GET['id'] ?? '';
$u_id = $_SESSION['user_id'];

if (!$id_discussion) {
    echo "No discussion specified";
    exit;
}

$stmt = $conn->prepare("SELECT d.*, u.username, u.id AS user_id  FROM discussion d JOIN users u ON d.id_user = u.id WHERE d.id=?");
$stmt->bind_param("s", $id_discussion);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Discussion not found";
    exit;
}

$discussion = $result->fetch_assoc();
$stmt->close();

$stmt2 = $conn->prepare("SELECT * FROM board WHERE id=?");
$stmt2->bind_param("i", $discussion['id_board']);
$stmt2->execute();
$result2 = $stmt2->get_result();

if ($result2->num_rows === 0) {
    echo "Board not found";
    exit;
}

$board = $result2->fetch_assoc();
$stmt2->close();

if ($result->num_rows === 0) {
    echo "Board not found";
    exit;
}

$stmt = $conn->prepare("
    SELECT ud.id, ud.content, u.username, u.id AS user_id
    FROM user_discussion ud
    JOIN users u ON ud.id_user = u.id
    WHERE ud.id_discussion = ?
    ORDER BY ud.id ASC
");

$stmt->bind_param("i", $discussion['id']);
$stmt->execute();

$result = $stmt->get_result();
$comments = $result->fetch_all(MYSQLI_ASSOC);

$stmt->close();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./style/style.css" />
    <link rel="stylesheet" href="./style/discussion.css" />
    <title>BitBug</title>
</head>
<body>
    <div id="container">
        <?php
            include "nav.php";
        ?>
        <main>
            <div class="dis-main-post">
                <div class="dis-header">
                    <div class="dis-header-levo">
                        <?php echo '<a href="./board.php?id='.$board['id'].'">Nazaj</a>' ?>
                        <h2><?php echo htmlspecialchars($board['title']) ?></h2>
                        <h1><?php echo htmlspecialchars($discussion["title"]) ?></h1>
                    </div>
                    <div class="dis-main-uporabnik">
                        <!--<p>1 January 2026 15.00</p>
                        <p>|</p>-->
                        <p><?php echo htmlspecialchars($discussion['username'])?></p>
                        <img src="./media/logo1Pixel.png" alt="logo">
                    </div>
                </div>
                <textarea disabled class="dis-wordarea" name="edit-comment-st_commenta" id=""><?php echo $discussion["content"] ?></textarea>
                <div class="dis-post-edit">
                    <form method="POST" <?php echo 'action="backend/php/discussionApi.php?type=DeleteDiscussion&id_dis='.$id_discussion.'"' ?> enctype="multipart/form-data">
                        <?php if ($discussion["user_id"] == $u_id): ?>
                            <input type="text" name="bT" value="<?php echo $board['title'] ?>" hidden>
                            <input style="width: fit-content;" type="submit" value="Delete">
                        <?php endif; ?>
                    </form>
                </div>
            </div>
            <h3><?php echo count($comments); ?> Comments</h3>
            <?php if (empty($comments)): ?>
                <p>No comments yet.</p>
            <?php else: ?>
                <?php foreach ($comments as $comment): ?>
                    <div class="dis-comment">
                        <div class="dis-header">
                            <div class="dis-header-levo">
                                
                            </div>
                            <div class="dis-main-uporabnik">
                                <!--<p>1 January 2026 15.00</p>
                                <p>|</p>-->
                                <p><?= htmlspecialchars($comment['username']) ?></p>
                                <img src="./media/logo1Pixel.png" alt="logo">
                            </div>
                        </div>
                        <form method="POST" <?php echo 'action="backend/php/discussionApi.php?type=DeleteComment&id_cmt='.$comment['id'].'&id_dis='.$id_discussion.'"' ?> enctype="multipart/form-data">
                            <textarea <?php /*if ($comment["user_id"] != $u_id) {*/ echo 'disabled'; /*}*/ ?> class="dis-wordarea" name="edit-comment-<?= $comment['id'] ?>"><?= htmlspecialchars($comment['content']) ?></textarea>
                            <div class="dis-post-edit">
                                <?php if ($comment["user_id"] == $u_id): ?>
                                    <input style="width: fit-content;" type="submit" value="Delete">
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
            <div class="dis-comment">
                <div class="dis-header">
                    <div class="dis-header-levo">
                        <h1>Add new comment</h1>
                    </div>
                    <div class="dis-main-uporabnik">
                        
                    </div>
                </div>
                <br>
                <form method="POST" <?php echo 'action="backend/php/discussionApi.php?type=NewComment&id_dis='.$id_discussion.'"' ?> enctype="multipart/form-data">
                    <textarea class="dis-wordarea" name="content" placeholder="Enter text"></textarea>
                    <div class="dis-post-edit">
                        <input type="submit">
                    </div>
                </form>
            </div>
        </main>
        <?php
            include "footer.php";
        ?>
    </div>

    <?php
    //var_dump($_SESSION);
    ?>
</body>
</html>