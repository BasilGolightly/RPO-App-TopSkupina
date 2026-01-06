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

if($result3->num_rows > 0){
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

if($result4->num_rows > 0){
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

if($uploadCount > 0){
    $hasAttachments = true;
    $uploads = $result5->fetch_all(MYSQLI_ASSOC);
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
                            <?php
                                if($isBoardPost){
                                    echo "
                                    <p id='small'>Board(s):</p>
                                    <br>
                                    <a id='small' href='board.php?title=" . htmlspecialchars($boards['title']) . "'>" . htmlspecialchars(" " . $boards['title']) . "</a>"
                                    ;    
                                }
                            ?>
                            
                        </div>
                        <p id="small">Tag(s): #tag1 #tag2</p>
                        <div id="post-info-row">
                            <?php
                            $pfp_path = "./media/pfp/";
                            $pfp_filename = "stock_pfp.png";
                            if($hasPfp){
                                $pfp_filename = $pfpAssoc["filename"] . "." . $pfpAssoc["extension"];
                            }
                            $pfp_path = $pfp_path . $pfp_filename;
                            //$pfp_path = "./media/pfp/" . $pfp_filename : "./media/roach_grayscale.jpg";
                            ?>
                            <img id="profile" src="<?= htmlspecialchars($pfp_path) ?>" alt="Profile">
                            <a id="big" style="margin-left: 20px;" <?php echo "href=profile.php?id='" . $author["id"] ."'";?>><?= htmlspecialchars($author['username']) ?></a>
                        </div>
                        <div id="post-info-row">
                            <p id="big" style="margin-right: 10px;">Rate: </p>
                            <h1>★★★☆☆</h1>
                        </div>
                    </div>
                    <div id="attachments" class="break-word">
                        <p id='big'>
                        <?php 
                            echo htmlspecialchars($uploadCount);
                            if($uploadCount == 1){
                                echo " Attachment"; 
                            }
                            else{
                                echo " Attachments";
                            }
                        ?>
                        </p>
                        <div id="post-info-row">
                            <?php
                                if($hasAttachments){
                                    $filePath = "./media/";
                                    if($isBoardPost){
                                        $filePath = $filePath . "board/";        
                                    }
                                    else{
                                        $filePath = $filePath . "post/";
                                    }
                                    foreach($uploads as $upload){
                                        $filename = "upload" . $upload["id"] . "." . $upload["extension"];
                                        $currPath = $filePath . $filename;
                                        echo "<a href='" . $currPath . "' target='__blank__'>" . $filename . "</a>";
                                    }
                                }
                                else{
                                    echo "<p>There are no attachments.</p>";
                                }
                            ?>

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
