<?php
    session_start();
    /*if(!isset($_SESSION['user_id']) || !isset($_SESSION['username']) || !isset($_SESSION['role'])){
        header("Location: login.php");
    }*/
?>

<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./style/style.css" />
    <link rel="stylesheet" href="./style/boards.css" />
    <script src="backend/js/boardsScript.js" defer></script>
    <title>Boards</title>
</head>
<body>
    <div id="container">
        <?php
            include "nav.php";
        ?>
        <main>
            <div id="ustvari-nov">
                <?php if (
                    isset($_SESSION['user_id'], $_SESSION['username'], $_SESSION['role'])
                ): ?>
                        <button id="novboard" >Create a new board <span class="plus">+</span></button>
                <?php else: ?>
                        To create a board please, <a href="login.php">Login</a>
                <?php endif; ?>
            </div>
            <div id="seznam-vrh">
                <input class="index-filter-checkbox" id="index-filter-boards-checkbox" type="checkbox">
                <label class="index-filter-label" for="index-filter-boards-checkbox">Boards ☰</label>
                <div class="index-filter-div" id="index-filter-boards-div">
                    <ul>
                        <li><button>Recent</button></li>
                        <li><button>Oldest</button></li>
                    </ul>
                </div>
            </div>
            <div id="seznam">
                <a class="objava">
                    <img src="./media/logo1Pixel.png" alt="logo">
                    <br>
                    <p>#coding, +1 file</p>
                    <h1>Naslov boarda</h1>
                    <br>
                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do </p>
                    <br>
                    <div class="objava-board-follow">
                        <button class="objava-board-follow-button">FOLLOW</button>
                    </div>
                </a>
            </div>

            <div id="seznam-vrh">
                <input class="index-filter-checkbox" id="index-filter-trending-checkbox" type="checkbox">
                <label class="index-filter-label" for="index-filter-trending-checkbox">Trending ☰</label>
                <div class="index-filter-div" id="index-filter-trending-div">
                    <ul>
                        <li><button>Most Popular</button></li>
                        <li><button>Rising</button></li>
                    </ul>
                </div>
            </div>
            <div id="seznam">
                <a class="objava">
                    <img src="./media/logo1Pixel.png" alt="logo">
                    <br>
                    <p>#video, +3 files</p>
                    <h1>Video urejanje</h1>
                    <br>
                    <p>Najboljše tehnike za montažo in postprodukcijo video vsebin</p>
                    <br>
                    <div class="objava-board-follow">
                        <button class="objava-board-follow-button">FOLLOW</button>
                    </div>
                </a>
            </div>
            <div id="boardobrazec">
                <form method="post" action="backend/php/createBoard.php" id="bObrazec">
                    <div class="title-wrap">
                        <span id="pencil">✎</span>
                        <h1 id="naslov">CREATE A NEW BOARD</h1>
                    </div>
                    <br><br>
                    <div class="input-wrap">
                        <input type="text" placeholder="Name" required>
                        <br>
                        <input type="text" placeholder="Tags">
                        <br>
                        <textarea name="description" id="description" rows="4" placeholder="Description"></textarea>
                    </div>
                    <div class="submit-wrap">
                        <input id="submitBoard" type="submit" value="CREATE BOARD">
                        <button id="cancelBoard" type="button">cancel</button>
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
