<?php
    $currentPage = basename($_SERVER['PHP_SELF']);
?>

<link rel="stylesheet" href="./style/nav.css" />

<nav>
    <div class="navLevo">
        <img class="logo" src="./media/logo1Pixel.png" alt="FileBug logo" />
        <input id="nav-menu-checkbox" type="checkbox">
        <label id="nav-menu-label" for="nav-menu-checkbox">Menu ☰</label>
        <ul>
            <li class="<?= $currentPage === 'Index.php' ? 'selected' : '' ?>">
                <a href="Index.php">Home</a>
            </li>

            <li class="<?= $currentPage === 'friends.php' ? 'selected' : '' ?>">
                <a href="friends.php">Friends</a>
            </li>

            <li class="<?= $currentPage === 'boards.php' ? 'selected' : '' ?>">
                <a href="boards.php">Boards</a>
            </li>

            <li>
                <a href="" id="nav-add-post">Add post</a>
            </li>

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
        <?php if (
            isset($_SESSION['user_id'], $_SESSION['username'], $_SESSION['role'])
        ): ?>
            <div class="nav-uporabnik">
                <h1 style="font-size: 1.2rem;"><a href="profile.php"><?php echo $_SESSION['username']?></a></h1>
                <br>
                <a id="nav-logout-button" href="./backend/php/logout.php">➜]</a>
            </div>
            <img class="logo" src="./media/logo1Pixel.png" alt="Uporabniska slika" />
        <?php else: ?>
            <div class="nav-uporabnik">
                <a href="login.php">Login</a>
            </div>
        <?php endif; ?>
    </div>
</nav>