<?php

require_once("Core/init.php");

$user = new User();
    if(!$user->isLoggedIn()){
        Redirect::to("login.php");
    }else{
        $_SESSION["user"] = escape($user->data()->username);
    }
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <link rel="stylesheet" href="./css/register.scss">
    <link rel="stylesheet" href="./css/reset.css">
    <link rel="stylesheet" href="./css/global.scss">
    <link rel="stylesheet" href="./css/exif.css">
    <title>Guard dashboard</title>
</head>
<body>
    <div class="main-container">
        <?php require_once("header.php"); ?>

	<div class="input-container">
                <p style="font-size:20px;" > Guard Dashboard: </p>
                <br>
            <a href="register.php">
                <img style=" align-content: justify; height:50px; width:50px;" src="./image/adduser.png">
				<div class="register-button">Pre-registration of Visitors </div>
			</a>
        </div>

        <div class="input-container">
            <a href="joseph_part/mainpage.php">
                <img style=" align-content: justify; height:50px; width:50px;" src="./image/warning.png">
                <div class="register-button">Report Incident </div>
            </a>
        </div>
                <br>


    </div>
</body>
</html>
