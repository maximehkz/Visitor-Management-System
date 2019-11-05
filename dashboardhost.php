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
    <title>Host Dahsboard</title>
</head>
<body>
    <div class="main-container">
        <?php require_once("header.php"); ?>
        <div class="input-container">
            <div class="input-icon-container">
                <img src="./image/adduser.png">
            </div>
            <a class="linkstyle" href="register.php">Pre-Registration of visitor </a>
            </div>

        <div class="input-container">
            <div class="input-icon-container">
                <img src="./image/warning.png">
            </div>
            <a class="linkstyle" href="incidentreport/mainpage.php"> Report incident </a>
        </div>
    </div>
</body>
</html>
