<?php 
    if(!isset($_SESSION['user_id']) || !isset($_SESSION['username']) || !isset($_SESSION['role'])){
        header("Location: login.php");
    }
?>

<link rel="stylesheet" href="./style/nav.css" />

<nav>
    <div class="navLevo">
        <img class="logo" src="./media/logo1Pixel.png" alt="FileBug logo" />
        <input id="nav-menu-checkbox" type="checkbox">
        <label id="nav-menu-label" for="nav-menu-checkbox">Menu ☰</label>
        <ul>
            <li class="selected"><a href="Index.php">Home</a></li>
            <li><a href="friends.php">Friends</a></li>
            <!--<li><a href="profile.php">My Profile</a></li>-->
            <li><a href="boards.php">Boards</a></li>
            <li><a href="" id="nav-add-post">Add post</a></li>

            <div id="nav-search-mobile">
                <input id="seznam-search" placeholder="Search" type="text">
                <button>🔍</button>
            </div>
        </ul>
    </div>
    <div class="navDesno">
        <div id="nav-search">
            <input id="seznam-search" placeholder="Search" type="text">
            <button>🔍</button>
        </div>
        <button style="display: none">Prijava</button>
        <div class="nav-uporabnik">
            <h1 style="font-size: 1.2rem;"><a href="profile.php"><?php echo $_SESSION['username']?></a></h1>
            <br>
            <a id="nav-logout-button" href="./backend/php/logout.php">➜]</a>
        </div>
        <img class="logo" src="./media/logo1Pixel.png" alt="Uporabniska slika" />
    </div>
</nav>