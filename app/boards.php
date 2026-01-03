<?php
    session_start();
    if(!isset($_SESSION['user_id']) || !isset($_SESSION['username']) || !isset($_SESSION['role'])){
        header("Location: login.php");
    }
?>

<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./style/style.css" />
    <link rel="stylesheet" href="./style/index.css" />
    <title>Boards</title>
</head>
<body>
    <div id="container">
        <?php
            include "nav.php";
        ?>
        <main>
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
        </main>
        <?php
            include "footer.php";
        ?>
    </div>
</body>
</html>
