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
                <input id="seznam-search" placeholder="Išči" type="text">
                <button>Išči</button>
            </div>
            <div id="seznam">
                <a class="objava">
                    <img src="./media/logo1Pixel.png" alt="logo">
                    <h1>Naslov objave</h1>
                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do </p>
                </a>
                <a class="objava">
                    <img src="./media/logo1Pixel.png" alt="logo">
                    <h1>Naslov objave</h1>
                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do </p>
                </a>
            </div>
        </main>
        <?php
            include "footer.php";
        ?>
    </div>
</body>
</html>