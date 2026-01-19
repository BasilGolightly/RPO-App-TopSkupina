<?php
session_start();
include "backend/php/conn.php";

$user_id = $_SESSION["user_id"] ?? "";

// get following
$stmt = $conn->prepare("
    SELECT u.id, u.username, up.filename, up.extension
    FROM follow f
    JOIN users u ON u.id = f.id_user2
    LEFT JOIN pfp p ON p.id_user = u.id
    LEFT JOIN upload up ON up.id = p.id_upload
    WHERE f.id_user1=?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result1 = $stmt->get_result();
$following = $result1->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// get followers
$stmt2 = $conn->prepare("
    SELECT u.id, u.username, up.filename, up.extension
    FROM follow f
    JOIN users u ON u.id = f.id_user1
    LEFT JOIN pfp p ON p.id_user = u.id
    LEFT JOIN upload up ON up.id = p.id_upload
    WHERE f.id_user2=?
");
$stmt2->bind_param("i", $user_id);
$stmt2->execute();
$result2 = $stmt2->get_result();
$followers = $result2->fetch_all(MYSQLI_ASSOC);
$stmt2->close();

// get friends
$stmt3 = $conn->prepare("
    SELECT u.id, u.username
    FROM follow f
    JOIN users u ON u.id = f.id_user2
    LEFT JOIN pfp p ON p.id_user = u.id
    LEFT JOIN upload up ON up.id = p.id_upload
    WHERE f.id_user1=?
    AND f.accepted=1
");
$stmt3->bind_param("i", $user_id);
$stmt3->execute();
$result3 = $stmt3->get_result();
$friends = $result3->fetch_all(MYSQLI_ASSOC);
$stmt3->close();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./style/style.css" />
    <link rel="stylesheet" href="./style/friends.css" />
    <title>Friends</title>
    <style>
    </style>
    <script src="backend/js/boardScript.js" defer></script>
</head>

<body>
    <div id="container">
        <?php
        include "nav.php";
        ?>
        <main>
            <div class="profileContent">
                <div class="container">
                    <div class="tab">
                        <button class="tablinks" onclick="openTab(event, 'Friends')" id="defaultOpen"
                            style="border-radius: 22px 0px 0px 0px;">Friends</button>
                        <button class="tablinks" onclick="openTab(event, 'Pending')">Followers</button>
                        <button class="tablinks" onclick="openTab(event, 'Sent')">Following</button>
                    </div>

                    <div id="Friends" class="tabcontent">
                        <div class="insidetab">
                            Friends list
                        </div>
                        <?php if (empty($friends)): ?>
                            <div class="insidetab">
                                <div class="friend">
                                    <p>No friends</p>
                                </div>
                            </div>
                        <?php else: ?>
                            <?php foreach ($friends as $friend): ?>
                                <div class="insidetab">
                                    <div class="friend">
                                        <?php
                                            $pfp_path = "./media/pfp/";
                                            $pfp_filename = "stock_pfp.png";
                                            if ($friend['filename']) {
                                                $pfp_filename = $friend["filename"] . "." . $friend["extension"];
                                            }
                                            $pfp_path = $pfp_path . $pfp_filename;
                                        ?>
                                        <div class="pfp">
                                            <img id="profile_img" src="<?= htmlspecialchars($pfp_path) ?>" alt="Profile">
                                        </div>
                                        <div class="username"><?php echo htmlspecialchars($friend['username']) ?></div>
                                        <div class="unfollow">
                                            <button class="unfollowbutton" type="button" id="unfollow">Unfollow</button>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <div id="Pending" class="tabcontent">
                        <div class="insidetab">Incoming requests</div>
                        <?php if (empty($followers)): ?>
                            <div class="insidetab">
                                <div class="friend">
                                    <p>No followers</p>
                                </div>
                            </div>
                        <?php else: ?>
                            <?php foreach ($followers as $follower): ?>
                                <div class="insidetab">
                                    <div class="friend">
                                        <?php
                                            $pfp_path = "./media/pfp/";
                                            $pfp_filename = "stock_pfp.png";
                                            if ($follower['filename']) {
                                                $pfp_filename = $follower["filename"] . "." . $follower["extension"];
                                            }
                                            $pfp_path = $pfp_path . $pfp_filename;
                                        ?>
                                        <div class="pfp">
                                            <img id="profile_img" src="<?= htmlspecialchars($pfp_path) ?>" alt="Profile">
                                        </div>
                                        <div class="username"><?php echo htmlspecialchars($follower['username']) ?></div>
                                        <div class="unfollow">
                                            <button class="followbutton" type="button" id="follow">Follow</button>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <div id="Sent" class="tabcontent">
                        <div class="insidetab">Sent requests</div>
                        <?php if (empty($following)): ?>
                            <div class="insidetab">
                                <div class="friend">
                                    <p>Not following anyone</p>
                                </div>
                            </div>
                        <?php else: ?>
                            <?php foreach ($following as $follow): ?>
                                <div class="insidetab">
                                    <div class="friend">
                                        <?php
                                            $pfp_path = "./media/pfp/";
                                            $pfp_filename = "stock_pfp.png";
                                            if ($follow['filename']) {
                                                $pfp_filename = $follow["filename"] . "." . $follow["extension"];
                                            }
                                            $pfp_path = $pfp_path . $pfp_filename;
                                        ?>
                                        <div class="pfp">
                                            <img id="profile_img" src="<?= htmlspecialchars($pfp_path) ?>" alt="Profile">
                                        </div>
                                        <div class="username"><?php echo htmlspecialchars($follow['username']) ?></div>
                                        <div class="unfollow">
                                            <button class="unfollowbutton" type="button" id="unfollow">Unfollow</button>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                </div>
            </div>
        </main>
        <?php
        include "footer.php";
        ?>
    </div>
</body>

    <script>
        function openTab(evt, currentTab) {
            var i, tabcontent, tablinks;
            tabcontent = document.getElementsByClassName("tabcontent");
            for (i = 0; i < tabcontent.length; i++) {
                tabcontent[i].style.display = "none";
            }
            tablinks = document.getElementsByClassName("tablinks");
            for (i = 0; i < tablinks.length; i++) {
                tablinks[i].className = tablinks[i].className.replace(" active", "");
            }
            document.getElementById(currentTab).style.display = "block";
            evt.currentTarget.className += " active";
        }

        document.getElementById("defaultOpen").click();
    </script>


</html>