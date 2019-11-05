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
    <div class="main-container dashboardguard-container">
        <?php require_once("header.php"); ?>
        <h1 style="font-weight:bold;"> Guard Dashboard: </h1>
	    <div class="input-container">
            <div class="input-icon-container">
                <img src="./image/adduser.png">
            </div>
            <a class="linkstyle" href="adhoc.html">  Registration on site   </a>
		    </div>

        <div class="input-container">
            <div class="input-icon-container">
                <img src="./image/adduser.png">
            </div>
            <a class="linkstyle" href="qrscanner.php"> Pre-Register Visitor in  </a>
        </div>

        <div class="input-container">
			   <div class="input-icon-container">
                <img src="./image/deleteuser1.png">
                </div>
			    <a class="linkstyle" href="qrscannerout.php">Pre-register visitor out </a>
		        </div>

        <div class="input-container">
            <div class="input-icon-container">
            <img src="./image/warning.png">
            </div>
            <a class="linkstyle" href="incidentreport/mainpage.php"> Report Incident </a>
            </div>

        <div class="input-container">
                <div class="input-icon-container">
                <img src="./image/submitreport.png">
                </div>
            <a class="linkstyle" href="incidentreport/reports.php"> Submit incident reported </a>
                </div>
        <div class="input-container">
            <div class="input-icon-container">
                <img src="./image/logout.png">
            </div>
            <a class="linkstyle" href="exif_clockout.php"> Log out </a>
            </div>
    </div>
</body>
</html>
