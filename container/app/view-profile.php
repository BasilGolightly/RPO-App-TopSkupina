<?php
session_start();
if (!isset($_SESSION["user_id"]) || !isset($_SESSION["username"]) || !isset($_SESSION["role"])) {
    header("Location: login.php");
}
$username = $_SESSION["username"] ?? "";
include "./backend/php/conn.php";

$user_id = $_GET["id"] ?? "";

// ce gledamo lastni profil, idi na profile.php za owner pogled
if($user_id == $_SESSION["user_id"]){
    header("Location: profile.php");
}

$sql = "SELECT users.username, users.description, users.privacy, upload.filename AS pfp_filename
        FROM users
        LEFT JOIN pfp on pfp.id_user = users.id
        LEFT JOIN upload ON upload.id = pfp.id_upload
        WHERE users.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();
$row = $result->fetch_assoc();

// dobimo description, filename in pa path
$description = $row["description"] ?? "";
$pfp_filename = $row["pfp_filename"] ?? "";
$privacyOption = $row["privacy"];
$username = $row["username"];
$pfp_path = $pfp_filename !== "" ? "media/pfp/" . $pfp_filename : "media/roach_grayscale.jpg";

// pridobivanje postov
$sql2 = "SELECT id, title, content
        FROM post
        WHERE id_user = ?
        ORDER BY id DESC";
$stmt2 = $conn->prepare($sql2);
$stmt2->bind_param("i", $user_id);
$stmt2->execute();

$result2 = $stmt2->get_result();
// vsi posti, v arrayu
$posts = $result2->fetch_all(MYSQLI_ASSOC);

$stmt->close();
$stmt2->close();

// ali followa?
$following = false;

$sql = "SELECT 1 
        FROM follow 
        WHERE id_user1 = ? 
          AND id_user2 = ?
        LIMIT 1";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $_SESSION['user_id'], $user_id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $following = true;
}

$stmt->close();

// št followerjev
$followers = 0;

$sql = "SELECT COUNT(*) 
        FROM follow 
        WHERE id_user2 = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($followers);
$stmt->fetch();
$stmt->close();

//izpis boardov
$boards = [];

$sql = "
    SELECT b.*, 
           COUNT(DISTINCT bf.id_user) AS followers,
           COUNT(DISTINCT bp.id_post) AS posts
    FROM board b
    LEFT JOIN board_follow bf ON bf.id_board = b.id
    LEFT JOIN board_post bp ON bp.id_board = b.id
    WHERE b.id_user = ?
    GROUP BY b.id
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$boards = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" type="text/css" href="style/view-profile.css">
    <link rel="stylesheet" type="text/css" href="style/style.css">
</head>

<body class="main">
    <div id="container">
        <?php
        include "nav.php";
        ?>
        <main>
            <div class="profileContent">
                <div class="container">
                    <!--ACCOUNT INFO-->
                    <div id="Account" class="tabcontent">
                        <div class="insidetab">
                            <img src="<?= htmlspecialchars($pfp_path) ?>" alt="Profile">
                            <div class="text"><?= htmlspecialchars($username ?? "") ?></div>
                            <?php if (!$following && $_SESSION["user_id"] != $user_id): ?>
                                <form method="POST" action="backend/php/view-profile.php?type=FollowUser&id=<?= $user_id ?>">
                                    <input style="width: fit-content;" type="submit" value="Follow">
                                </form>
                            <?php elseif($following && $_SESSION["user_id"] != $user_id): ?>
                                <form method="POST" action="backend/php/view-profile.php?type=UnfollowUser&id=<?= $user_id ?>">
                                    <input style="width: fit-content;" type="submit" value="Unfollow">
                                </form>
                            <?php endif; ?>
                            <p style="font-size: 1rem;"><strong><?php echo $followers ?></strong> followers</p>
                        </div>
                        <div class="insidetab">About me</div>
                        <div class="insidetab">
                            <textarea style="max-height: 5rem;" readonly id="about-text" name="description" rows="5"
                            cols="50"><?= htmlspecialchars($description ?? "") ?></textarea>
                        </div>
                    </div>
                    <div>
                        <h1 style="margin-top: 3rem;">Boards</h1>
                        <div>
                            <div id="seznam">
                                <?php if (empty($boards)): ?>
                                    <p>This user has not created any boards.</p>
                                <?php else: ?>
                                    <?php foreach ($boards as $board): ?>
                                        <a class="objava" href="board.php?title=<?= urlencode($board['title']) ?>">
                                            <h2><?= htmlspecialchars($board['title']) ?></h2>

                                            <p style="font-size: 1rem;">
                                                Followers: <strong><?= (int)$board['followers'] ?></strong>
                                                · Posts: <strong><?= (int)$board['posts'] ?></strong>
                                            </p>

                                            <p><?= htmlspecialchars($board['description']) ?></p>
                                        </a>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
        <?php
        include "footer.php";
        ?>
    </div>
</body>

</html>