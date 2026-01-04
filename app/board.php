
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

            <!-- gumb ustvarjanje posta -->
        <div id="ustvari-nov">
    <?php if (isset($_SESSION['user_id'])): ?>
       
        <button id="novpost">Create a new post <span class="plus">+</span></button>
    <?php else: ?>
        To create a post, <a href="login.php">Login</a>
    <?php endif; ?>
</div>


            <div class="vb-content">
                <div class="posts">
                    <h1>Recent posts</h1>
                    <div id="seznam">
                        <a class="objava">
                            <img src="./media/logo1Pixel.png" alt="logo">
                            <h1>Naslov objave</h1>
                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do </p>
                            <div id="objava-zvezdice">★★★☆☆</div>
                            <div id="objava-info">
                                <div id="objava-info-levo">
                                    <p>1M views</p>
                                    <p>#coding, +1 file</p>
                                </div>
                                <div id="objava-info-desno">
                                    <p>Janez Novak</p>
                                    <img src="./media/logo1Pixel.png" alt="logo">
                                </div>
                            </div>
                        </a>
                        <a class="objava">
                            <img src="./media/logo1Pixel.png" alt="logo">
                            <h1>Naslov objave</h1>
                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do </p>
                            <div id="objava-zvezdice">★★★☆☆</div>
                            <div id="objava-info">
                                <div id="objava-info-levo">
                                    <p>1M views</p>
                                    <p>#coding, +1 file</p>
                                </div>
                                <div id="objava-info-desno">
                                    <p>Janez Novak</p>
                                    <img src="./media/logo1Pixel.png" alt="logo">
                                </div>
                            </div>
                        </a>
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
                <form method="POST" action="backend/php/createPost.php" id="pObrazec">
                    <div class="title-wrap">
                        <span id="pencil">✎</span>
                        <h1 id="naslov">CREATE A NEW POST</h1>
                    </div>
                    <br><br>
                    <div class="input-wrap">
                        <input type="text" placeholder="Title" required name="title">
                        <br>
                        <textarea name="content"  rows="4" placeholder="Post Content"></textarea>
                    </div>
                    <div class="submit-wrap">
                        <input type="submit" value="CREATE POST">
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
