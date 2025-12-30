<link rel="stylesheet" href="./style/nav.css" />

<nav>
    <div class="navLevo">
        <img class="logo" src="./media/logo1Pixel.png" alt="FileBug logo" />
        <input id="nav-menu-checkbox" type="checkbox">
        <label id="nav-menu-label" for="nav-menu-checkbox">Menu ☰</label>
        <ul>
            <li class="selected"><a href="">Home</a></li>
            <li><a href="">Friends</a></li>
            <li><a href="">My Profile</a></li>
            <li><a href="">Boards</a></li>
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
            <h1 style="font-size: 1.2rem;"><a href="">Janez Novak</a></h1>
            <a id="nav-logout-button">➜]</a>
        </div>
        <img class="logo" src="./media/logo1Pixel.png" alt="Uporabniska slika" />
    </div>
</nav>