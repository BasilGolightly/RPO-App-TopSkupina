<?php
    session_start();
    include "backend/php/conn.php";
    //include __DIR__ . "/backend/php/conn.php";

    $sql = "
    SELECT *
    FROM board
    ";
    $all = $conn->query($sql);

    $flw = null;
    $myb = null;
    if(isset($_SESSION['user_id'], $_SESSION['username'], $_SESSION['role'])){
        $stm1 = $conn->prepare("
            SELECT b.*, 
                COUNT(DISTINCT bf2.id_user) AS followers,
                COUNT(DISTINCT bp.id_post) AS posts
            FROM board b
            JOIN board_follow bf ON bf.id_board = b.id
            LEFT JOIN board_follow bf2 ON bf2.id_board = b.id
            LEFT JOIN board_post bp ON bp.id_board = b.id
            WHERE bf.id_user = ?
            GROUP BY b.id
        ");
        $stm1->bind_param("i", $_SESSION['user_id']);
        $stm1->execute();
        $flw = $stm1->get_result();
        $stm1->close();

        $stm2 = $conn->prepare("
            SELECT b.*, 
                COUNT(DISTINCT bf.id_user) AS followers,
                COUNT(DISTINCT bp.id_post) AS posts
            FROM board b
            LEFT JOIN board_follow bf ON bf.id_board = b.id
            LEFT JOIN board_post bp ON bp.id_board = b.id
            WHERE b.id_user = ?
            GROUP BY b.id
        ");
        $stm2->bind_param("i", $_SESSION['user_id']);
        $stm2->execute();
        $myb = $stm2->get_result();
        $stm2->close();
    }

    $sql1 = "
    SELECT b.*, 
       COUNT(DISTINCT bp.id_post) AS posts,
       COUNT(DISTINCT bf.id_user) AS followers
    FROM board b
    LEFT JOIN board_post bp ON bp.id_board = b.id
    LEFT JOIN board_follow bf ON bf.id_board = b.id
    GROUP BY b.id
    ORDER BY posts DESC
    LIMIT 5
    "; // kej jih 5 pase v eno vrstico
    $top5 = $conn->query($sql1);

    $sql2 = "
    SELECT b.*, 
       COUNT(DISTINCT bp.id_post) AS posts,
       COUNT(DISTINCT bf.id_user) AS followers
    FROM board b
    LEFT JOIN board_post bp ON bp.id_board = b.id
    LEFT JOIN board_follow bf ON bf.id_board = b.id
    GROUP BY b.id
    ORDER BY followers DESC
    LIMIT 5
    "; // kej jih 5 pase v eno vrstico
    $mfl5 = $conn->query($sql2);

    $sql3 = "
    SELECT b.*, 
       COUNT(DISTINCT bf.id_user) AS followers,
       COUNT(DISTINCT bp.id_post) AS posts
    FROM board b
    LEFT JOIN board_follow bf ON bf.id_board = b.id
    LEFT JOIN board_post bp ON bp.id_board = b.id
    GROUP BY b.id
    ORDER BY b.created DESC
    LIMIT 5
    "; // kej jih 5 pase v eno vrstico
    $new5 = $conn->query($sql3);

    $sql4 = "
    SELECT bt.id_board, t.name
    FROM board_tag bt
    JOIN tag t ON t.id = bt.id_tag
    ";
    $tags = $conn->query($sql4);

    $fst = null;
    if (isset($_SESSION['user_id'])) {
        $stm4 = $conn->prepare("
            SELECT id_board
            FROM board_follow
            WHERE id_user = ?
        ");
        $stm4->bind_param("i", $_SESSION['user_id']);
        $stm4->execute();
        $fst = $stm4->get_result();
        $stm4->close();
    }

    if (!$top5 || !$mfl5 || !$new5 || !$tags) {
        die("Query failed: " . $conn->error);
    }

    $tagsByBoard = [];

    while ($row = $tags->fetch_assoc()) {
        $tagsByBoard[$row['id_board']][] = $row['name'];
    }

    $followMap = [];

    if ($fst) {
        while ($row = $fst->fetch_assoc()) {
            $followMap[(int)$row['id_board']] = true;
        }
    }

    //search
    $searchQuery = trim($_GET['q'] ?? '');

    $isTagSearch = str_starts_with($searchQuery, '#');

    if ($isTagSearch) {
        $searchTag = strtolower(ltrim($searchQuery, '#')); // remove # and lowercase
    }


    if (!empty($searchQuery)) {
        if ($isTagSearch) {
            $stmt = $conn->prepare("
                SELECT b.*, 
                    COUNT(DISTINCT bf.id_user) AS followers,
                    COUNT(DISTINCT bp.id_post) AS posts
                FROM board b
                JOIN board_tag bt ON bt.id_board = b.id
                JOIN tag t ON t.id = bt.id_tag
                LEFT JOIN board_follow bf ON bf.id_board = b.id
                LEFT JOIN board_post bp ON bp.id_board = b.id
                WHERE t.name = ?
                GROUP BY b.id
            ");
            $stmt->bind_param("s", $searchTag);
            $stmt->execute();
            $searchResults = $stmt->get_result();
        }
        else {
            $stmt = $conn->prepare("
                SELECT b.*, 
                    COUNT(DISTINCT bf.id_user) AS followers,
                    COUNT(DISTINCT bp.id_post) AS posts
                FROM board b
                LEFT JOIN board_follow bf ON bf.id_board = b.id
                LEFT JOIN board_post bp ON bp.id_board = b.id
                WHERE b.title LIKE ? OR b.description LIKE ?
                GROUP BY b.id
            ");

            $likeQuery = '%' . $searchQuery . '%';
            $stmt->bind_param("ss", $likeQuery, $likeQuery);
            $stmt->execute();
            $searchResults = $stmt->get_result();
        }
    }

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
            $searchContext = 'boards';
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

            <!--search-->
            <?php if (!empty($_GET['q']) && ($_GET['context'] ?? '') === 'boards'): ?>
                <?php if ($searchResults->num_rows === 0): ?>
                    <p>No boards found.</p>
                <?php else: ?>
                <details class="board-section" open>
                    <summary>
                        <span>Search results for "<?= htmlspecialchars($searchQuery) ?>"</span>
                        <form action="boards.php">
                            <button type= "submit">Clear search</button>
                        </form>
                    </summary>

                    <div class="section-content">
                        <!-- board -->
                        <div id="seznam">
                            <?php while ($board = $searchResults->fetch_assoc()): ?>
                                <div class="boardRow">
                                        <a class="objava" href="board.php?title=<?= urlencode($board['title']) ?>">
                                            <h2><?= htmlspecialchars($board['title']) ?></h2>

                                            <p class="small">
                                                Followers: <?= (int)$board['followers'] ?>
                                                · Posts: <?= (int)$board['posts'] ?>
                                            </p>

                                            <p><?= htmlspecialchars($board['description']) ?></p>

                                            <p class="tags">
                                                <?php foreach ($tagsByBoard[$board['id']] ?? [] as $tag): ?>
                                                    <span class="tag">#<?= htmlspecialchars($tag) ?></span>
                                                <?php endforeach; ?>
                                            </p>
                                        </a>

                                        <?php if (isset($_SESSION['user_id'])): ?>
                                        <form method="post" action="backend/php/toggleFollowBoard.php" class="followFrm">
                                            <input type="hidden" name="board_id" value="<?= $board['id'] ?>">
                                            <input type="hidden" name="url" value="<?= $_SERVER['PHP_SELF'] ?>">
                                            <button class="followBtn">
                                                <?= $isFollowing ? 'Unfollow' : 'Follow' ?>
                                            </button>
                                        </form>
                                        <?php endif; ?>
                                    </div>
                            <?php endwhile; ?>
                        </div>
                    </div>
                </details>
                <?php endif; ?>
            <?php endif; ?>

            <!--followed-->
            <?php if ($flw && $flw->num_rows > 0): ?>
                <details class="board-section" open>
                    <summary>
                        <span>Followed boards</span>
                    </summary>

                    <div class="section-content">
                        <!-- board -->
                        <div id="seznam">
                            <?php while ($board = $flw->fetch_assoc()): $isFollowing = isset($followMap[$board['id']]); ?>
                                <div class="boardRow">
                                    <a class="objava" href="board.php?title=<?= urlencode($board['title']) ?>">
                                        <h2><?= htmlspecialchars($board['title']) ?></h2>

                                        <p class="small">
                                            Followers: <?= (int)$board['followers'] ?>
                                            · Posts: <?= (int)$board['posts'] ?>
                                        </p>

                                        <p><?= htmlspecialchars($board['description']) ?></p>

                                        <p class="tags">
                                            <?php foreach ($tagsByBoard[$board['id']] ?? [] as $tag): ?>
                                                <span class="tag">#<?= htmlspecialchars($tag) ?></span>
                                            <?php endforeach; ?>
                                        </p>
                                    </a>

                                    <?php if (isset($_SESSION['user_id'])): ?>
                                        <form method="post" action="backend/php/toggleFollowBoard.php" class="followFrm">
                                            <input type="hidden" name="board_id" value="<?= $board['id'] ?>">
                                            <input type="hidden" name="url" value="<?= $_SERVER['PHP_SELF'] ?>">
                                            <button class="followBtn">
                                                <?= $isFollowing ? 'Unfollow' : 'Follow' ?>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    </div>
                </details>
            <?php endif; ?>

            <!--my boards-->
            <?php if ($myb && $myb->num_rows > 0): ?>
                <details class="board-section" open>
                    <summary>
                        <span>My boards</span>
                    </summary>

                    <div class="section-content">
                        <!-- board -->
                        <div id="seznam">
                            <?php while ($board = $myb->fetch_assoc()): $isFollowing = isset($followMap[$board['id']]); ?>
                                <div class="boardRow">
                                    <a class="objava" href="board.php?title=<?= urlencode($board['title']) ?>">
                                        <h2><?= htmlspecialchars($board['title']) ?></h2>

                                        <p class="small">
                                            Followers: <?= (int)$board['followers'] ?>
                                            · Posts: <?= (int)$board['posts'] ?>
                                        </p>

                                        <p><?= htmlspecialchars($board['description']) ?></p>

                                        <p class="tags">
                                            <?php foreach ($tagsByBoard[$board['id']] ?? [] as $tag): ?>
                                                <span class="tag">#<?= htmlspecialchars($tag) ?></span>
                                            <?php endforeach; ?>
                                        </p>
                                    </a>

                                    <?php if (isset($_SESSION['user_id'])): ?>
                                    <form method="post" action="backend/php/toggleFollowBoard.php" class="followFrm">
                                        <input type="hidden" name="board_id" value="<?= $board['id'] ?>">
                                            <input type="hidden" name="url" value="<?= $_SERVER['PHP_SELF'] ?>">
                                        <button class="followBtn">
                                            <?= $isFollowing ? 'Unfollow' : 'Follow' ?>
                                        </button>
                                    </form>
                                    <?php endif; ?>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    </div>
                </details>
            <?php endif; ?>

            <!--top 5 boards-->
            <details class="board-section" open>
                <summary>
                    <span>Top 5 boards</span>
                </summary>

                <div class="section-content">
                    <!-- board -->
                    <div id="seznam">
                        <?php while ($board = $top5->fetch_assoc()): $isFollowing = isset($followMap[$board['id']]); ?>
                            <div class="boardRow">
                                <a class="objava" href="board.php?title=<?= urlencode($board['title']) ?>">
                                    <h2><?= htmlspecialchars($board['title']) ?></h2>

                                    <p class="small">
                                        Followers: <?= (int)$board['followers'] ?>
                                        · Posts: <?= (int)$board['posts'] ?>
                                    </p>

                                    <p><?= htmlspecialchars($board['description']) ?></p>

                                    <p class="tags">
                                        <?php foreach ($tagsByBoard[$board['id']] ?? [] as $tag): ?>
                                            <span class="tag">#<?= htmlspecialchars($tag) ?></span>
                                        <?php endforeach; ?>
                                    </p>
                                </a>

                                <?php if (isset($_SESSION['user_id'])): ?>
                                    <form method="post" action="backend/php/toggleFollowBoard.php" class="followFrm">
                                        <input type="hidden" name="board_id" value="<?= $board['id'] ?>">
                                            <input type="hidden" name="url" value="<?= $_SERVER['PHP_SELF'] ?>">
                                        <button class="followBtn">
                                            <?= $isFollowing ? 'Unfollow' : 'Follow' ?>
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>
            </details>

            <!--top 5 most followed boards-->
            <details class="board-section" open>
                <summary>
                    <span>Top 5 most followed boards</span>
                </summary>

                <div class="section-content">
                    <!-- board -->
                    <div id="seznam">
                        <?php while ($board = $mfl5->fetch_assoc()): $isFollowing = isset($followMap[$board['id']]); ?>
                            <div class="boardRow">
                                <a class="objava" href="board.php?title=<?= urlencode($board['title']) ?>">
                                    <h2><?= htmlspecialchars($board['title']) ?></h2>

                                    <p class="small">
                                        Followers: <?= (int)$board['followers'] ?>
                                        · Posts: <?= (int)$board['posts'] ?>
                                    </p>

                                    <p><?= htmlspecialchars($board['description']) ?></p>

                                    <p class="tags">
                                        <?php foreach ($tagsByBoard[$board['id']] ?? [] as $tag): ?>
                                            <span class="tag">#<?= htmlspecialchars($tag) ?></span>
                                        <?php endforeach; ?>
                                    </p>
                                </a>

                                <?php if (isset($_SESSION['user_id'])): ?>
                                    <form method="post" action="backend/php/toggleFollowBoard.php" class="followFrm">
                                        <input type="hidden" name="board_id" value="<?= $board['id'] ?>">
                                            <input type="hidden" name="url" value="<?= $_SERVER['PHP_SELF'] ?>">
                                        <button class="followBtn">
                                            <?= $isFollowing ? 'Unfollow' : 'Follow' ?>
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>
            </details>

            <!--top 5 new boards-->
            <details class="board-section" open>
                <summary>
                    <span>Top 5 newest boards</span>
                </summary>

                <div class="section-content">
                    <!-- board -->
                    <div id="seznam">
                        <?php while ($board = $new5->fetch_assoc()): $isFollowing = isset($followMap[$board['id']]); ?>
                            <div class="boardRow">
                                <a class="objava" href="board.php?title=<?= urlencode($board['title']) ?>">
                                    <h2><?= htmlspecialchars($board['title']) ?></h2>

                                    <p class="small">
                                        Followers: <?= (int)$board['followers'] ?>
                                        · Posts: <?= (int)$board['posts'] ?>
                                    </p>

                                    <p><?= htmlspecialchars($board['description']) ?></p>

                                    <p class="tags">
                                        <?php foreach ($tagsByBoard[$board['id']] ?? [] as $tag): ?>
                                            <span class="tag">#<?= htmlspecialchars($tag) ?></span>
                                        <?php endforeach; ?>
                                    </p>
                                </a>

                                <?php if (isset($_SESSION['user_id'])): ?>
                                    <form method="post" action="backend/php/toggleFollowBoard.php" class="followFrm">
                                        <input type="hidden" name="board_id" value="<?= $board['id'] ?>">
                                            <input type="hidden" name="url" value="<?= $_SERVER['PHP_SELF'] ?>">
                                        <button class="followBtn">
                                            <?= $isFollowing ? 'Unfollow' : 'Follow' ?>
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>
            </details>

            <!--all boards-->
            <details class="board-section" open>
                <summary>
                    <span>All boards</span>
                </summary>

                <div class="section-content">
                    <!-- board -->
                    <div id="seznam">
                        <?php while ($board = $all->fetch_assoc()): $isFollowing = isset($followMap[$board['id']]); ?>
                            <div class="boardRow">
                                <a class="objava" href="board.php?title=<?= urlencode($board['title']) ?>">
                                    <h2><?= htmlspecialchars($board['title']) ?></h2>

                                    <p class="small">
                                        Followers: <?= (int)$board['followers'] ?>
                                        · Posts: <?= (int)$board['posts'] ?>
                                    </p>

                                    <p><?= htmlspecialchars($board['description']) ?></p>

                                    <p class="tags">
                                        <?php foreach ($tagsByBoard[$board['id']] ?? [] as $tag): ?>
                                            <span class="tag">#<?= htmlspecialchars($tag) ?></span>
                                        <?php endforeach; ?>
                                    </p>
                                </a>

                                <?php if (isset($_SESSION['user_id'])): ?>
                                    <form method="post" action="backend/php/toggleFollowBoard.php" class="followFrm">
                                        <input type="hidden" name="board_id" value="<?= $board['id'] ?>">
                                            <input type="hidden" name="url" value="<?= $_SERVER['PHP_SELF'] ?>">
                                        <button class="followBtn">
                                            <?= $isFollowing ? 'Unfollow' : 'Follow' ?>
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>
            </details>

            <!-- Nov board -->
            <div id="boardobrazec">
                <form method="post" action="backend/php/createBoard.php" id="bObrazec">
                    <div class="title-wrap">
                        <span id="pencil">✎</span>
                        <h1 id="naslov">CREATE A NEW BOARD</h1>
                    </div>
                    <br><br>
                    <div class="input-wrap">
                        <input type="text" placeholder="Name" required name="title">
                        <br>
                        <input type="text" placeholder="Tags" name="tags">
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
