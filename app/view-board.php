<?php
    session_start();
    if(!isset($_SESSION['user_id']) || !isset($_SESSION['username']) || !isset($_SESSION['role'])){
        header("Location: login.php");
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./style/style.css" />
    <link rel="stylesheet" href="./style/view-board.css" />
    <title>BitBug</title>
</head>
<body>
    <div id="container">
        <?php
            include "nav.php";
        ?>
        <main>
            <input type="checkbox" name="" id="vb-cb-hide-info">
            <div id="board-info">
                <h1>Board</h1>
                <p class="vb-tags-views"><strong>#board</strong>, 1M followers</p>
                <p class="vb-description">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
                <div id="objava-zvezdice">★★★☆☆</div>
                <div class="vb-users">
                    <div class="vb-user-profile">
                        <img src="./media/logo1Pixel.png" alt="logo">
                        <div class="vb-user-text">
                            <p class="vb-username">USER</p>
                            <p class="vb-role">admin</p>
                        </div>
                    </div>
                    <div class="vb-user-profile">
                        <img src="./media/logo1Pixel.png" alt="logo">
                        <div class="vb-user-text">
                            <p class="vb-username">USER</p>
                            <p class="vb-role">admin</p>
                        </div>
                    </div>
                </div>
            </div>
            <label class="vb-hide-info-label" for="vb-cb-hide-info"></label>
            <div class="vb-content">
                <div class="vb-recent-posts">
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