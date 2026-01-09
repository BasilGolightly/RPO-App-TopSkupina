<?php
session_start();
include "backend/php/conn.php";

$post_id = $_GET['id'] ?? '';

if (!$post_id) {
    $error = "No post specified";
    header("Location: index.php?error=" . urlencode($error));
}

// (1) POST INFO

$stmt = $conn->prepare("SELECT * FROM post WHERE id=?");
$stmt->bind_param("s", $post_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Post not found";
    exit;
}

$post = $result->fetch_assoc();
$stmt->close();

// (2) USER / AUTHOR INFO

$stmt2 = $conn->prepare("SELECT * FROM `users` WHERE id=?");
$stmt2->bind_param("i", $post['id_user']);
$stmt2->execute();
$result2 = $stmt2->get_result();

if ($result2->num_rows === 0) {
    $error = "Author not found.";
    header("Location: index.php?error=" . urlencode($error));
}

$author = $result2->fetch_assoc();
$stmt2->close();

$isBoardPost = false;
$hasPfp = false;
$hasAttachments = false;
$hasComments = false;

// (3) BOARD

$stmt3 = $conn->prepare("
    SELECT b.id, b.title
    FROM board b
    JOIN board_post bp ON bp.id_board = b.id
    WHERE bp.id_post = ?
");
$stmt3->bind_param("i", $post['id']);
$stmt3->execute();
$result3 = $stmt3->get_result();

if ($result3->num_rows > 0) {
    $isBoardPost = true;
}

$boards = $result3->fetch_assoc();
$stmt3->close();

// (4) PFP

$stmt4 = $conn->prepare("SELECT `filename`, `extension` 
FROM upload JOIN pfp
    ON upload.id = pfp.id_upload
WHERE upload.id_user = ?");

$stmt4->bind_param("i", $author["id"]);
$stmt4->execute();
$result4 = $stmt4->get_result();
$pfpAssoc;

if ($result4->num_rows > 0) {
    $hasPfp = true;
    $pfpAssoc =  $result4->fetch_assoc();
}

$stmt4->close();

$description = $boards["description"] ?? "";

// (5) ATTACHMENTS

$stmt5 = $conn->prepare("SELECT * 
FROM upload JOIN post_upload 
    ON upload.id = post_upload.id_upload
WHERE post_upload.id_post = ?");
$stmt5->bind_param("i", $post["id"]);
$stmt5->execute();
$result5 = $stmt5->get_result();
$uploadCount = $result5->num_rows;
$uploads;

if ($uploadCount > 0) {
    $hasAttachments = true;
    $uploads = $result5->fetch_all(MYSQLI_ASSOC);
}

// (6) COMMENTS
$stmt6 = $conn->prepare(
    "
    SELECT c.id AS commentId, c.id_comment as parentId, 
    c.content, u.id AS id_user, u.username AS username, 
    filename, extension
    FROM comment AS c JOIN users AS u
        ON c.id_user = u.id LEFT JOIN pfp AS p
        ON u.id = p.id_user LEFT JOIN upload up
        ON p.id_upload = up.id 
    WHERE c.id_post = ?
    "
);

$stmt6->bind_param("i", $post_id);
$stmt6->execute();
$result6 = $stmt6->get_result();
$comments;
if ($result6->num_rows > 0) {
    $hasComments = true;
    $comments = $result6->fetch_all(MYSQLI_ASSOC);
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./style/style.css" />
    <link rel="stylesheet" href="./style/post.css" />
    <title>
        <?php
        echo $post["title"] . " by " . $author["username"];
        ?>
    </title>
    <style>
    </style>
    <script src="backend/js/boardScript.js" defer></script>
    <script src="./backend/js/postScript.js" defer></script>
</head>

<body>
    <div id="container">
        <?php
        include "nav.php";
        ?>
        <main>
            <div id="post-container">
                <!--POST CONTENT-->
                <div id="post-info-container">
                    <!-- TEXT -->
                    <div id="post-info" class="break-word">

                        <!--TITLE-->
                        <form method="post" class="titleCon" id="titleForm" action="./backend/php/editTitle.php">
                            <input type="hidden" name="post_id" value="<?= $post_id ?>">
                            <input type="text" class="readonly" name="title" id="title" readonly value="<?= htmlspecialchars($post['title']) ?>">
                            <?php if ($post["id_user"] == $author["id"]): ?>
                                <br>
                                <button type="button" class="editBtn" id="titleBtn">Edit title</button>
                            <?php endif; ?>
                        </form>
                        <!--TITLE-->

                        <!--DESCRIPTION-->
                        <form method="post" id="contentForm" class="contentCon" action="./backend/php/editContent.php">
                            <input type="hidden" name="post_id" value="<?= $post_id ?>">
                            <p id="big" style="margin-bottom: 1px;">Content</p>
                            <textarea id="content" name="content" class="readonly" readonly><?= htmlspecialchars($post['content']) ?></textarea>
                            <?php if ($post["id_user"] == $author["id"]): ?>
                                <br>
                                <button type="button" class="editBtn" id="contentBtn">Edit Content</button>
                            <?php endif; ?>
                        </form>
                        <!--DESCRIPTION-->

                        <!--BOARDS-->
                        <div id="post-info-row">
                            <?php
                            if ($isBoardPost) {
                                echo "
                                    <p id='small'>Board(s):</p>
                                    <br>
                                    <a id='small' target='__blank__' href='board.php?title=" . htmlspecialchars($boards['title']) . "'>" . htmlspecialchars(" " . $boards['title']) . "</a>";
                            }
                            ?>
                        </div>
                        <!--BOARDS-->

                        <!--TAGS-->
                        <p id="small">Tag(s): #tag1 #tag2</p>
                        <div id="post-info-row">
                            <?php
                            $pfp_path = "./media/pfp/";
                            $pfp_filename = "stock_pfp.png";
                            if ($hasPfp) {
                                $pfp_filename = $pfpAssoc["filename"] . "." . $pfpAssoc["extension"];
                            }
                            $pfp_path = $pfp_path . $pfp_filename;
                            //$pfp_path = "./media/pfp/" . $pfp_filename : "./media/roach_grayscale.jpg";
                            ?>
                            <img id="profile" src="<?= htmlspecialchars($pfp_path) ?>" alt="Profile">
                            <a id="big" style="margin-left: 20px;" <?php echo "href=profile.php?id='" . $post['id_user'] . "'"; ?>><?= htmlspecialchars($author['username']) ?></a>
                        </div>
                        <!--TAGS-->

                        <!--RATING-->
                        <?php if ($post["id_user"] != $author["id"]): ?>
                            <div id="post-info-row">
                                <p id="big" style="margin-right: 10px;">Rate: </p>
                                <h1>★★★☆☆</h1>
                            </div>
                        <?php endif; ?>
                    </div>
                    <!-- TEXT -->

                    <!--FILES-->
                    <div id="attachments" class="break-word">
                        <p id='big'>
                            <?php
                            echo htmlspecialchars($uploadCount);
                            if ($uploadCount == 1) {
                                echo " Attachment";
                            } else {
                                echo " Attachments";
                            }
                            ?>
                        </p>
                        <?php if ($post["id_user"] == $author["id"] && $uploadCount < 5): ?>
                            <form id="fileForm" method="post" action="./backend/php/editFiles.php" enctype="multipart/form-data">
                                <div class="file-title">
                                    UPLOAD FILES
                                    <br>
                                    <span class="sub"><i>(max. 5 MB, <span id="fileCount"><?= 5 - $uploadCount ?></span> files allowed)</i></span>
                                </div>
                                <div class="file-wrap">
                                    <?php if ($isBoardPost): ?>
                                        <input type="hidden" name="is_board_post" value="1">
                                    <?php else: ?>
                                        <input type="hidden" name="is_board_post" value="0">
                                    <?php endif; ?>
                                    <input type="hidden" name="allowed_count" value="<?= 5 - $uploadCount ?>">
                                    <input type="hidden" name="post_id" value="<?= $post_id ?>">
                                    <label for="bFiles" class="custom-file-upload postUpload">
                                        <img src="./media/logo1.png" height="13" width="13"><span id="uploadText"> Choose files</span>
                                    </label>
                                    <input type="file" id="bFiles" name="bFiles[]" multiple>
                                    <button class="fileUploadBtn" type="submit">UPLOAD FILES</button>
                                </div>
                            </form>
                        <?php endif; ?>
                        <div class="attachments-container">
                            <?php
                            if ($hasAttachments) {
                                $filePath = "./media/";
                                if ($isBoardPost) {
                                    $filePath = $filePath . "board/";
                                } else {
                                    $filePath = $filePath . "post/";
                                }
                                foreach ($uploads as $upload) {
                                    $filename = "upload" . $upload["id"] . "." . $upload["extension"];
                                    $currPath = $filePath . $filename;
                                    echo "
                                    <div class='post-info-row'>
                                        <a href='" . $currPath . "' target='__blank__'>" . $filename . "</a>" .
                                        "</div>";
                                    //echo "<br>";
                                }
                            } else {
                                echo "<p>There are no attachments.</p>";
                            }
                            ?>
                        </div>

                    </div>
                    <!--FILES-->
                </div>
                <!--POST CONTENT-->

                <!--COMMENTS-->
                <div id="comments-container">
                    <p id="big" style="margin-left: 10px;">Comments</p>
                    <!-- COMMENTS -->
                    <div id="comments">
                        <?php if (!$hasComments): ?>
                            <p id="emptyCommentsMsg">No comments yet. Be the first one!</p>
                        <?php else: ?>
                            <?php foreach ($comments as $comment): ?>
                                <div id="comment">
                                    <div id="comment-user">
                                        <?php if (empty($comment["filename"])): ?>
                                            <img id="comment-profile" src="<?= htmlspecialchars("./media/pfp/stock_pfp.png") ?>" alt="Profile">
                                        <?php else: ?>
                                            <img id="comment-profile" src="<?= htmlspecialchars("./media/pfp/" . $comment["filename"] . "." . $comment["extension"]) ?>" alt="Profile">
                                        <?php endif; ?>
                                        <a style="margin-left: 20px;" <?php echo "href='profile.php?id=" . $comment["id_user"] . "'"; ?>><?= htmlspecialchars($comment["username"]) ?></a>
                                    </div>
                                    <div id="comment-content">

                                        <p><?= htmlspecialchars($comment["content"]) ?></p>
                                        <?php if (isset($_SESSION["user_id"]) && (int)$comment["id_user"] === (int)$_SESSION["user_id"]): ?>

                                            <form id="edit_comment_form" method="post" action="./backend/php/edit_post_comment.php">
                                                <button class="edit_button" type="button" id="edit_comment_button"> Edit</button>
                                                <input type="hidden" name="comment_id" value=<?= htmlspecialchars($comment["commentId"]) ?>>
                                                <input type="hidden" name="post_id" value="<?= htmlspecialchars($post_id) ?>">
                                                <div id="edit_comment_container" class="hidden">
                                                    <input type="text" name="new_comment" id="edit_comment_label">
                                                </div>
                                            </form>
                                            <form method="post" action="./backend/php/delete_post_comment.php" class="comment-delete">
                                                <input type="hidden" name="comment_id" value=<?= htmlspecialchars($comment["commentId"]) ?>>
                                                <input type="hidden" name="post_id" value="<?= htmlspecialchars($post_id) ?>">
                                                <button type="submit" class="delete-button">Delete</button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    <!-- COMMENTS -->

                    <!-- ADD COMMENT -->
                    <form method="post" id="comment-form" action="./backend/php/createComment.php" style="align-items: center; margin-top: 10px;">
                        <input type="hidden" name="p-Id" id="post-id" value="<?= $post_id ?>" readonly>
                        <input type="hidden" name="c-Id" id="parent-comment">
                        <textarea name="new-comment" id="new-comment" placeholder="Add comment..." id="add-comment" required></textarea>
                        <br>
                        <input type="submit" id="submit-btn" value="Komentiraj" class="commentBtn postButtons">
                    </form>
                    <?php if ($post["id_user"] != $author["id"]): ?>
                        <button class="postButtons" id="report_button" type="button">Report</button>
                    <?php endif; ?>
                    <!-- ADD COMMENT, report -->

                    <!--DELETE POST-->
                    <?php if ($post["id_user"] == $author["id"]): ?>


                        <form method="post" action="./backend/php/delete_post.php" style="display: inline;">
                            <input type="hidden" name="post_id" value="<?= htmlspecialchars($post_id) ?>">
                            <button class="postButtons delete-button" id="delete_button" type="submit">Delete post</button>
                        </form>
                    <?php endif; ?>
                    <!--DELETE POST-->
                </div>
                <!--COMMENTS-->
            </div>
        </main>
        <?php
        include "footer.php";
        ?>
    </div>
    <script>
        let error = "<?= htmlspecialchars($_SESSION["error"]) ?>";
        if (error.trim() != "") {
            alert(error);
        }

        document.querySelectorAll("#edit_comment_form").forEach((form) => {
            const btn = form.querySelector("#edit_comment_button");
            const container = form.querySelector("#edit_comment_container");
            const label = form.querySelector("#edit_comment_label");
            if (!btn || !container || !label) {
                return;
            }
            let is_input_hidden = true;
            btn.addEventListener("click", () => {
                if (is_input_hidden) {
                    container.classList.remove("hidden");
                    label.focus();
                    is_input_hidden = false;
                } else {
                    form.submit();
                }
            });
        });
    </script>
</body>

</html>