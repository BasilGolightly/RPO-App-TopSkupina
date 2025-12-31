<?php
session_start();
if (!isset($_SESSION["user_id"]) || !isset($_SESSION["username"]) || !isset($_SESSION["role"])) {
    header("Location: login.html");
}
$username = $_SESSION["username"] ?? "";
include "./backend/php/conn.php";

$user_id = $_SESSION["user_id"] ?? "";
$sql = "SELECT description FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();
$row = $result->fetch_assoc();

// dobimo description iz db
$description = $row["description"] ?? "";

$stmt->close();
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
    <div class="container">
        <div class="tab">
            <button class="tablinks" onclick="openTab(event, 'Account')" id="defaultOpen" style="border-radius: 22px 0px 0px 0px;">My Account</button>
            <button class="tablinks" onclick="openTab(event, 'Settings')">Settings</button>
            <button class="tablinks" onclick="openTab(event, 'Posts')">Posts</button>
        </div>

        <div id="Account" class="tabcontent">
            <div class="insidetab">Account details</div>
            <hr>

            <div class="insidetab">
                <img src="media/roach_grayscale.jpg" alt="Profile">
                <div class="text"><?= htmlspecialchars($username ?? "") ?></div>
                <button class="editbutton">Change profile picture</button>
            </div>
            <div class="insidetab">
                <div>
                    <?= htmlspecialchars($username ?? "") ?>
                </div>
                <button class="editbutton">Change username</button>
            </div>
            <div class="insidetab">
                Password: ********
                <button class="showbutton">show</button>
                <button class="editbutton">Change password</button>
            </div>
            <div class="insidetab">
                About me
                <button class="editbutton" type="button" id="about-edit" style="margin-left: 10px; width: 60px;">Edit</button>
            </div>
            <div class="insidetab">
                <form method="post" action="./backend/php/update_description.php">
                    <textarea readonly id="about-text" name="description" rows="10" cols="50"><?= htmlspecialchars($description ?? "") ?></textarea>
                    <button type="submit" class="savebutton">Save</button>
                </form>
            </div>
        </div>

        <div id="Settings" class="tabcontent">
            <div class="insidetab">Settings</div>
            <hr>
        </div>

        <div id="Posts" class="tabcontent">
            <div class="insidetab">Posts</div>
            <hr>
        </div>

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

        document.getElementById("defaultOpen").click();

        // funkcionalnost edit buttona
        const editBtn = document.getElementById("about-edit");
        const aboutText = document.getElementById("about-text");


        editBtn.addEventListener("click", () => {
            aboutText.removeAttribute("readonly");
            aboutText.focus();
        });
    </script>
</body>

</html>