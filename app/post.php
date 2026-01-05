<?php
session_start();
include "backend/php/conn.php";

$title = $_GET['title'] ?? '';

if (!$title) {
    echo "No post specified";
    exit;
}

$stmt = $conn->prepare("SELECT * FROM post WHERE title=?");
$stmt->bind_param("s", $title);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Post not found";
    exit;
}

$post = $result->fetch_assoc();
$stmt->close();

$stmt2 = $conn->prepare("SELECT * FROM `users` WHERE id=?");
$stmt2->bind_param("i", $post['id_user']);
$stmt2->execute();
$result2 = $stmt2->get_result();

if ($result2->num_rows === 0) {
    echo "Author not found";
    exit;
}

$author = $result2->fetch_assoc();
$stmt2->close();

$stmt = $conn->prepare("
    SELECT b.id, b.title
    FROM board b
    JOIN board_post bp ON bp.id_board = b.id
    WHERE bp.id_post = ?
");
$stmt->bind_param("i", $post['id']);
$stmt->execute();
$result = $stmt->get_result();

$boards = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();


$description = $row["description"] ?? "";
$pfp_filename = $row["pfp_filename"] ?? "";
$pfp_path = $pfp_filename !== "" ? "media/pfp/" . $pfp_filename : "media/roach_grayscale.jpg";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./style/style.css" />
    <link rel="stylesheet" href="./style/post.css" />
    <title>BitBug</title>
    <style>  
    </style>
    <script src="backend/js/boardScript.js" defer></script>
</head>
<body>
    <div id="container">
        <?php
            include "nav.php";
        ?>
        <main>
            <div id="post-container">
                <div id="post-info-container">
                    <div id="post-info" class="break-word">
                        <h1 id='title'><?= htmlspecialchars($post['title']) ?></h1>

                        <p id="big" style="margin-bottom: 1px;">Description</p>
                        <p><?= htmlspecialchars($post['content']) ?></p>

                        <div id="post-info-row">
                            <p id="small">Board(s):</p>
                            <?php foreach ($boards as $board): ?>
                                <p id="small"> #<?= htmlspecialchars($board['title']) ?></p>
                            <?php endforeach; ?>
                        </div>
                        <p id="small">Tag(s): #tag1 #tag2</p>
                        <div id="post-info-row">
                            <img id="profile" src="<?= htmlspecialchars($pfp_path) ?>" alt="Profile">
                            <p id="big" style="margin-left: 20px;"><?= htmlspecialchars($author['username']) ?></p>
                        </div>
                        <div id="post-info-row">
                            <p id="big" style="margin-right: 10px;">Rate: </p>
                            <h1>★★★☆☆</h1>
                        </div>
                    </div>
                    <div id="attachments" class="break-word">
                        <p id='big'>Attachment(s)</p>
                        <div id="post-info-row">
                            <p>(Attachment here)</p>
                        </div>
                    </div>
                </div>
                <div id="comments-container">
                    <p id="big" style="margin-left: 10px;">Comments</p>
                    <div id="comments">
                        <div id="comment">
                            <div id="comment-user">
                                <img id="comment-profile" src="<?= htmlspecialchars($pfp_path) ?>" alt="Profile">
                                <p style="margin-left: 20px;">Username1</p>
                            </div>
                            <div id="comment-content">
                                <p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit.</p>
                            </div>
                        </div>
                        <div id="comment" style="border-radius: 12px;">
                            <div id="comment-user">
                                <img id="comment-profile" src="<?= htmlspecialchars($pfp_path) ?>" alt="Profile">
                                <p style="margin-left: 20px;">Username2</p>
                            </div>
                            <div id="comment-content">
                                <p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit.</p>
                            </div>
                        </div>
                    </div>
                    <div style="align-items: center; margin-top: 10px;">
                        <input type="text" name="add_comment" placeholder="Add comment..." id="add_comment">
                    </div>
                    <button id="report_button">Report</button> 
                </div>
            </div>
        </main>
        <?php
            include "footer.php";
        ?>
    </div>
</body>
</html>
