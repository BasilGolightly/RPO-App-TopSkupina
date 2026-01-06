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
$stmt->close();

$stmt2 = $conn->prepare("SELECT * FROM `users` WHERE id=?");
$stmt2->bind_param("i", $board['id_user']);
$stmt2->execute();
$result2 = $stmt2->get_result();

if ($result2->num_rows === 0) {
    echo "Author not found";
    exit;
}

$author = $result2->fetch_assoc();
$stmt2->close();

$stmt = $conn->prepare("
    SELECT p.id, p.title, p.content, p.id_user
    FROM post p
    JOIN board_post bp ON bp.id_post = p.id
    WHERE bp.id_board = ?
");
$stmt->bind_param("i", $board['id']);
$stmt->execute();
$result = $stmt->get_result();

$posts = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

if(isset($_GET['error']) && $_GET['error'] !== ""){
    echo '<script language="javascript">';
    echo 'alert("' . $_GET['error'] . '");';
    echo '</script>';
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./style/style.css" />
    <link rel="stylesheet" href="./style/board.css" />
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
            <input type="checkbox" name="" id="vb-cb-hide-info">
            <div id="board-info">
                <?php echo "<h1 class='title'>" . htmlspecialchars($board['title']) . "</h1>"; ?>
                <p class="vb-tags-views"><strong>TBA</strong>, followers</p>
                <?php echo "<p class='vb-description'>" . htmlspecialchars($board['description']) . "</p>"; ?>
                <div class="vb-users">
                    <div class="vb-user-profile">
                        <img src="./media/logo1Pixel.png" alt="logo">
                        <div class="vb-user-text">
                            <?php echo "<p class='vb-username'>" . htmlspecialchars($author['username']) . "</p>"; ?>
                            <p class="vb-role">Creator</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="vb-content">
                <div class="posts">
                        <div id="ustvari-nov">
                        <?php if (isset($_SESSION['user_id'])): ?>
                        
                            <button id="novpost">Create a new post <span class="plus">+</span></button>
                        <?php else: ?>
                            To create a post, <a href="login.php">Login</a>
                        <?php endif; ?>
                    </div>
                    <h1>Recent posts</h1>
                    <div id="seznam">
                        <?php if (empty($posts)): ?>
                            <p>No posts yet.</p>
                        <?php else: ?>
                            <?php foreach ($posts as $post): ?>
                                <a href="post.php?id=<?= urlencode($post['id']) ?>" class="objava">
                                    <h2><?= htmlspecialchars($post['title']) ?></h2>
                                    <p><?= htmlspecialchars($post['content']) ?></p>
                                </a>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="vb-discussions">
                    <h1>Discussions</h1>
                    <ul>
                        <li>
                            <h2><a href="">Discussion topic 1</a></h2>
                            <p>10 comments</p>
                            <div class="vb-d-user-info">
                                <p>Janez Novak</p>
                                <img src="./media/logo1Pixel.png" alt="logo">
                            </div>
                        </li>
                        <li>
                            <h2><a href="">Discussion topic 1</a></h2>
                            <p>10 comments</p>
                            <div class="vb-d-user-info">
                                <p>Janez Novak</p>
                                <img src="./media/logo1Pixel.png" alt="logo">
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Obrazec za ustvarjanje posta v skritem stanju -->

            <div id="postobrazec">
                <form method="POST" action="backend/php/createPost.php" id="pObrazec" enctype="multipart/form-data">
                    <input type="text" name="bID" value="<?php echo $board['id'] ?>" hidden>
                    <input type="text" name="bT" value="<?php echo $board['title'] ?>" hidden>
                    <div class="title-wrap">
                        <span id="pencil">📨</span>
                        <h1 id="naslov">CREATE A NEW POST</h1>
                    </div>
                    <br><br>
                    <div class="input-wrap">
                        <input type="text" placeholder="Title" required name="title">
                        <br>
                        <textarea name="content"  rows="4" placeholder="Post Content" required></textarea>
                    </div>
                    <div class="file-title">
                        UPLOAD FILES 
                        <br>
                        <span class="sub"><i>(max. 5 MB, 5 files)</i></span>
                    </div>
                    <div class="file-wrap">
                        <label for="bFiles" class="custom-file-upload">
                            <img src="./media/logo1.png" height="13" width="13"><span id="uploadText"> Upload files</span>
                        </label>
                        <input type="file" id="bFiles" name="bFiles[]" multiple>
                    </div>
                    <div class="submit-wrap">
                        <input id="submitPost" type="submit" value="POST TO BOARD">
                        <button id="cancelPost" type="button">cancel</button>
                    </div>
                </form>
            </div>

        </main>
        <?php
            include "footer.php";
        ?>
    </div>
</body>
</html>
