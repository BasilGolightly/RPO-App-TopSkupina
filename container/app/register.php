<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<?php
if(isset($_SESSION['registerError']) && $_SESSION['registerError'] != ""){
    echo '<script language="javascript">';
    echo 'alert("' . $_SESSION['registerError'] . '");';
    echo '</script>';
}
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" type="text/css" href="style/register.css">
    <link rel="stylesheet" type="text/css" href="style/style.css">
    <script src="backend/js/registerScript.js" defer></script>
</head>

<body>
    <!--MAIN FRAME-->
    <form class="main" enctype="multipart/form-data" method="post" action="backend/php/register.php">
        <div class="container">
            <!--CARD-->
            <div class="card">
                <!--LOGO-->
                <div class="top">
                    <img src="media/logo1Pixel.png" class="logoLong">
                </div>
                <!--LOGO-->

                <!--TITLE-->
                <div class="mid">
                    <div class="header">
                        BitBug
                    </div>
                    <div class="sub">
                        Register
                    </div>
                </div>
                <!--TITLE-->

                <!--FORM-->
                <div class="formContainer">
                    <div class="usernameContainer">
                        <div>
                            Username
                        </div>
                        <div>
                            <input type="text" name="username" id="username" required maxlength="50"
                                autocomplete="username" placeholder="Enter your username">
                        </div>
                    </div>
                    <div class="passContainer">
                        <div class="passFlex">
                            <div>
                                Password
                            </div>
                            <div class="passInner">
                                <div>
                                    <input type="password" name="password" id="password" required maxlength="50"
                                        placeholder="New password" autocomplete="new-password">
                                </div>
                                <div class="checkCon">
                                    <div>
                                        <input type="checkbox" onclick="togglePassword(this, 'password')">
                                    </div>
                                    <div>
                                        Show
                                        <div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="repeatPassContainer passContainer">
                                <div class="passFlex">
                                    <div>
                                        Repeat Password
                                    </div>
                                    <div class="passInner">
                                        <div>
                                            <input type="password" name="repeatPass" id="repeatPass" required
                                                maxlength="50" placeholder="Repeat password"
                                                autocomplete="new-password">
                                        </div>
                                        <div class="checkCon">
                                            <div>
                                                <input type="checkbox" onclick="togglePassword(this, 'repeatPass')">
                                            </div>
                                            <div>
                                                Show
                                                <div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>

                        </div>
                    </div>
                    <div class="submitContainer">
                        <div>
                            <input type="submit" value="Register" id="registerBtn">
                        </div>
                    </div>
                    <div class="loginContainer">
                        <div class="loginInner">
                            Already have an account?&ThickSpace;<a href="./login.php">Login here</a>
                        </div>
                    </div>
                </div>
                <!--FORM-->
            <!--CARD-->
    </form>
    <!--MAIN FRAME-->
</body>

</html>