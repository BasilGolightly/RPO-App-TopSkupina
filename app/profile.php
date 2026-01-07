<?php
session_start();
if (!isset($_SESSION["user_id"]) || !isset($_SESSION["username"]) || !isset($_SESSION["role"])) {
    header("Location: login.php");
}
$username = $_SESSION["username"] ?? "";
include "./backend/php/conn.php";

$user_id = $_SESSION["user_id"] ?? "";
$sql = "SELECT users.description, users.privacy, upload.filename AS pfp_filename
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
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" type="text/css" href="style/profile.css">
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
                    <div class="tab">
                        <button class="tablinks" onclick="openTab(event, 'Account')" id="defaultOpen"
                            style="border-radius: 22px 0px 0px 0px;">My Account</button>
                        <button class="tablinks" onclick="openTab(event, 'Settings')">Settings</button>
                        <button class="tablinks" onclick="openTab(event, 'Posts')">Posts</button>
                    </div>

                    <div id="Account" class="tabcontent">
                        <div class="insidetab">Account details</div>
                        <hr>

                        <div class="insidetab">
                            <img src="<?= htmlspecialchars($pfp_path) ?>" alt="Profile">
                            <div class="text"><?= htmlspecialchars($username ?? "") ?></div>
                            <form id="pfp-form" method="post" action="./backend/php/change_pfp.php" enctype="multipart/form-data">
                                <input id="pfp-input" type="file" name="pfp" accept="image/*" class="pfp-hidden">
                                <button class="editbutton" type="button" id="pfp-btn">Change profile picture</button>
                            </form>
                        </div>
                        <div class="insidetab username-section">
                            <div>
                                Username:
                                <?= htmlspecialchars($username ?? "") ?>
                            </div>
                            <?php
                            if (!empty($_SESSION["error"])): ?>
                                <div class="error"><?= htmlspecialchars($_SESSION["error"]) ?></div>
                                <?php unset($_SESSION["error"]); ?>
                            <?php endif; ?>
                            <form id="username-change-form" method="post" action="./backend/php/change_username.php">
                                <button class="editbutton" type="button" id="username-change-btn">Change username</button>
                                <div id="username-change-container" class="hidden">
                                    <input type="text" name="new_username" placeholder="New username" id="username-change-label">
                                </div>
                            </form>
                        </div>
                        <div class="insidetab">
                            Password: ********
                            <form id="password-change-form" method="post" action="./backend/php/change_password.php">
                                <button class="editbutton" type="button" id="password-change-btn">Change password</button>
                                <div id="password-change-container" class="hidden">
                                    <input type="text" name="new_password" placeholder="New password" id="password-change-label">
                                </div>
                            </form>
                        </div>
                        <div class="insidetab">
                            About me
                            <button class="editbutton" type="button" id="about-edit"
                                style="margin-left: 10px; width: 60px;">Edit</button>
                        </div>
                        <div class="insidetab">
                            <form method="post" action="./backend/php/update_description.php">
                                <textarea readonly id="about-text" name="description" rows="10"
                                    cols="50"><?= htmlspecialchars($description ?? "") ?></textarea>
                                <button type="submit" class="savebutton">Save</button>
                            </form>
                        </div>
                    </div>

                    <div id="Settings" class="tabcontent">
                        <div class="insidetab">Settings</div>
                        <hr>

                        <div class="settings-container">
                            <form id="user-settings-form" method="post" action="./backend/php/update_settings.php">
                                <div class="settings-row">
                                    <label for="account-visibility">Account visibility</label>
                                    <select id="account-visibility" name="visibility" class="visibility-dropdown">
                                        <option value="public" <?php if ($privacyOption === "public") echo " selected"; ?>>Public</option>
                                        <option value="private" <?php if ($privacyOption === "private") echo " selected"; ?>>Private</option>
                                        <option value="friends" <?php if ($privacyOption === "friends") echo " selected"; ?>>Friends only</option>
                                    </select>
                                </div>

                                <div class="settings-description">
                                    <strong>Private:</strong> Only users you follow can view your personal posts. If you wish to be a board, every member of the respective board can see your posts. <br>
                                    <strong>Public:</strong> Your posts are visible to everyone, including users who do not follow you. <br>
                                    <strong>Friends only:</strong> Only users you have added as friends can view your personal posts.
                                </div>

                                <button type="submit" class="savebutton settings-save">Save changes</button>
                            </form>

                            <hr class="settings-divider">

                            <button class="editbutton settings-action" onclick="location.href='friends.php'">
                                Manage friends
                            </button>

                            <button class="editbutton settings-action" onclick="location.href='boards.php'">
                                Manage boards
                            </button>

                            <button class="editbutton delete-account" onclick="confirmDelete()">
                                Delete account
                            </button>
                        </div>
                    </div>

                    <div id="Posts" class="tabcontent">
                        <div class="insidetab">Posts</div>
                        <hr>
                        <?php if (empty($posts)): ?>
                            <p>No posts yet</p>
                        <?php else: ?>
                            <?php foreach ($posts as $post): ?>
                                <a href="post.php?id=<?= urlencode($post['id']) ?>" class="profile-post">
                                    <h2><?= htmlspecialchars($post['title']) ?></h2>
                                    <p><?= htmlspecialchars($post['content']) ?></p>
                                </a>
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

        function confirmDelete() {
            if (confirm("Are you sure you want to delete your account? This action cannot be undone.")) {
                location.href = './backend/php/delete_account.php';
            }
        }

        document.getElementById("defaultOpen").click();

        // funkcionalnost edit buttona
        const editBtn = document.getElementById("about-edit");
        const aboutText = document.getElementById("about-text");


        editBtn.addEventListener("click", () => {
            aboutText.removeAttribute("readonly");
            aboutText.focus();
        });


        // spreminjanje usernama || passworda
        function change_username_or_password({
            btnId,
            containerId,
            labelId,
            formId,
            saveText
        }) {
            let isInputHidden = true;
            const btn = document.getElementById(btnId);
            const container = document.getElementById(containerId);
            const label = document.getElementById(labelId);
            const form = document.getElementById(formId);

            btn.addEventListener("click", () => {
                if (isInputHidden) {
                    container.classList.remove("hidden");
                    label.focus();
                    btn.textContent = saveText;
                    isInputHidden = false;
                } else {
                    form.submit();
                }
            });
        }

        change_username_or_password({
            btnId: "username-change-btn",
            containerId: "username-change-container",
            labelId: "username-change-label",
            formId: "username-change-form",
            saveText: "Save username"
        });

        change_username_or_password({
            btnId: "password-change-btn",
            containerId: "password-change-container",
            labelId: "password-change-label",
            formId: "password-change-form",
            saveText: "Save password"
        });


        // spreminjanje pfp-ja
        const pfp_input = document.getElementById("pfp-input");
        const pfp_btn = document.getElementById("pfp-btn");
        const pfp_form = document.getElementById("pfp-form");

        pfp_btn.addEventListener("click", () => pfp_input.click());
        pfp_input.addEventListener("change", () => {
            if (pfp_input.files.length > 0) {
                pfp_form.submit();
            }
        })
    </script>
</body>

</html>