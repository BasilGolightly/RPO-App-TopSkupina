<?php
session_start();
include "backend/php/conn.php";

$bId = $_GET['id'] ?? '';

if (!$bId) {
    echo "No board specified";
    exit;
}

$stmt = $conn->prepare("
SELECT b.*, 
    COUNT(DISTINCT bp.id_post) AS posts,
    COUNT(DISTINCT bf.id_user) AS followers
FROM board b
LEFT JOIN board_post bp ON bp.id_board = b.id
LEFT JOIN board_follow bf ON bf.id_board = b.id
WHERE b.id = ?
GROUP BY b.id
");
$stmt->bind_param("i", $bId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Board not found";
    exit;
}

$sql = $conn->prepare("
    SELECT bt.id_board, t.name
    FROM board_tag bt
    JOIN tag t ON t.id = bt.id_tag
    WHERE bt.id_board = ?
");
$sql->bind_param("i", $bId);
$sql->execute();
$tags = $sql->get_result();
$sql->close();

$bTags = [];

while ($row = $tags->fetch_assoc()) {
    $bTags[$row['id_board']][] = $row['name'];
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

$stmt = $conn->prepare("
    SELECT d.id, d.title, u.username
    FROM discussion d
    JOIN users u ON d.id_user = u.id
    WHERE d.id_board = ?
");
$stmt->bind_param("i", $board['id']);
$stmt->execute();
$result = $stmt->get_result();

$discussions = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$stmt = $conn->prepare("
    SELECT *
    FROM discussion d
    JOIN user_discussion ud ON d.id = ud.id_discussion
    WHERE d.id_board = ?
");
$stmt->bind_param("i", $board['id']);
$stmt->execute();
$result = $stmt->get_result();
$comments = $result->fetch_all(MYSQLI_ASSOC);
$commentCount = count($comments);
$stmt->close();

$stmt = $conn->prepare("
    SELECT 
        d.id,
        d.title,
        u.username,
        COUNT(ud.id_discussion) AS comment_count
    FROM discussion d
    JOIN users u ON d.id_user = u.id
    LEFT JOIN user_discussion ud ON d.id = ud.id_discussion
    WHERE d.id_board = ?
    GROUP BY d.id
");
$stmt->bind_param("i", $board['id']);
$stmt->execute();
$result = $stmt->get_result();
$discussions = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$isFollowing = false;

if (isset($_SESSION['user_id'])) {
    $stmt = $conn->prepare("
        SELECT 1 
        FROM board_follow 
        WHERE id_user = ? AND id_board = ?
    ");
    $stmt->bind_param("ii", $_SESSION['user_id'], $bId);
    $stmt->execute();
    $stmt->store_result();
    $isFollowing = $stmt->num_rows > 0;
    $stmt->close();
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

                <div class="vb-header">
                    <h1 class="title"><?= htmlspecialchars($board['title']) ?></h1>

                    <?php if (
                        isset($_SESSION['user_id']) &&
                        $_SESSION['user_id'] === (int)$board['id_user']
                    ): ?>
                        <button id="editBoardBtn" class="vb-edit">Edit</button>
                    <?php endif; ?>
                </div>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <form method="post" action="backend/php/toggleFollowBoard.php" class="followFrm">
                        <input type="hidden" name="board_id" value="<?= (int)$board['id'] ?>">
                        <input type="hidden" name="url" value="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>">
                        <button class="followBtn">
                            <?= $isFollowing ? 'Unfollow' : 'Follow' ?>
                        </button>
                    </form>
                <?php endif; ?>

                <div class="vb-tags">
                    <?php if ($tags->num_rows === 0) {
                        echo "This board doesn't have tags.";
                    }
                    foreach ($bTags[$board['id']] ?? [] as $tag): ?>
                        <span>
                            #<?= htmlspecialchars($tag) ?>
                        </span>
                    <?php endforeach; ?>
                </div>

                <p class="vb-tags-views">
                    <strong><?= htmlspecialchars($board['followers']) ?></strong> followers
                </p>

                <p class="vb-description">
                    <?= htmlspecialchars($board['description']) ?>
                </p>

                <div class="vb-users">
                    <div class="vb-user-profile">
                        <img src="./media/logo1Pixel.png" alt="logo">
                        <div class="vb-user-text">
                            <a class="vb-username" href="view-profile.php?id=<?= $author['id'] ?>"><?= htmlspecialchars($author['username']) ?></a>
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
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <button id="dis-new-button">Create discussion</button>
                    <?php else: ?>
                        To create a discussion, <a href="login.php">Login</a>
                    <?php endif; ?>
                    <br>
                    <br>
                    <h1>Discussions</h1>
                    <ul>
                        <?php if (empty($discussions)): ?>
                            <p>No discussions yet.</p>
                        <?php else: ?>
                            <?php foreach ($discussions as $discussion): ?>
                                <li>
                                    <h2>
                                        <a href="discussion.php?id=<?= urlencode($discussion['id']) ?>">
                                            <?= htmlspecialchars($discussion['title']) ?>
                                        </a>
                                    </h2>

                                    <p><?= (int)$discussion['comment_count'] ?> comments</p>

                                    <div class="vb-d-user-info">
                                        <p><?= htmlspecialchars($discussion['username']) ?></p>
                                        <img src="./media/logo1Pixel.png" alt="logo">
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        <?php endif; ?>
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

            <div id="postobrazec-discussion">
                <form method="POST" action="backend/php/discussionApi.php?type=NewDis" id="pObrazec" enctype="multipart/form-data">
                    <input type="text" name="bID" value="<?php echo $board['id'] ?>" hidden>
                    <input type="text" name="bT" value="<?php echo $board['title'] ?>" hidden>
                    <div class="title-wrap">
                        <span id="pencil">📨</span>
                        <h1 id="naslov">CREATE A NEW DISCUSSION</h1>
                    </div>
                    <br><br>
                    <div class="input-wrap">
                        <input type="text" placeholder="Title" required name="title">
                        <br>
                        <textarea name="content"  rows="4" placeholder="Post Content" required></textarea>
                    </div>
                    <div class="submit-wrap">
                        <input id="submitPost-dis" type="submit" value="CREATE DISCUSSION">
                        <button id="cancelPost-dis" type="button">cancel</button>
                    </div>
                </form>
            </div>
            
            <?php if (
                isset($_SESSION['user_id']) &&
                $_SESSION['user_id'] === (int)$board['id_user']
            ): ?>
            <div id="editobrazec">
                <form id="editBoardForm" method="post" action="backend/php/updateBoard.php" style="display:none;">
                    <input type="hidden" name="board_id" value="<?= $bId ?>">

                    <label>
                        Title
                        <br>
                        <input type="text" name="title" value="<?= htmlspecialchars($board['title']) ?>">
                    </label>

                    <label>
                        Description
                        <br>
                        <textarea name="description"><?= htmlspecialchars($board['description']) ?></textarea>
                    </label>

                    <label>
                        Tags
                        <br>
                        <input type="text" name="tags"
                            value="<?= htmlspecialchars(implode(', ', $bTags[$board['id']] ?? [])) ?>">
                    </label>

                    <button type="submit">Save</button>
                    <button type="button" id="cancelEdit">Cancel</button>
                    <button type="button" id="deleteBoardBtn" class="deletebtn" data-board-id="<?= (int)$board['id'] ?>"> Delete board </button>

                </form>
            </div>
            <?php endif; ?>

        </main>
        <?php
            include "footer.php";
        ?>
    </div>
</body>
</html>
