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
    <link rel="stylesheet" href="./style/index.css" />
    <title>BitBug</title>
</head>
<body>
    <div id="container">
        <?php
            include "nav.php";
        ?>
        <main>
            <div id="seznam-vrh">
                <input class="index-filter-checkbox" id="index-filter-objave-checkbox" type="checkbox">
                <label class="index-filter-label" for="index-filter-objave-checkbox">Recent posts ☰</label>
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
                        <button class="objava-board-follow-button">Follow</button>
                    </div>
                </a>
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