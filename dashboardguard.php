<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <link rel = "stylesheet" href="./css/register.css">
    <link rel = "stylesheet" href="./css/reset.css">
    <link rel = "stylesheet" href="./css/global.css">
    <link rel = "stylesheet" href="./css/exif.css">
    <title>Guard dashboard</title>
</head>
<body>
    <div class="main-container">
        <?php require_once("header.php"); ?>

	<div class="input-container">
                <p style="font-size:20px;" > Guard Dashboard: </p>
                <br>
            <a href="">
				<div class="register-button">Registration on site </div>
			</a>

		</div>
                <br>
        <div class="input-container">
			<a href="">
				<div class="register-button">Pre-register visitor in </div>
			</a>
        </div>
                    <br>
        <div class="input-container">
			<a href="">
				<div class="register-button">Pre-register visitor out </div>
			</a>
        </div>
                <br>
        <div class="input-container">
            <a href="">
                <div class="register-button">Report Incident </div>
            </a>
        </div>
                <br>
        <div class="input-container">
            <a href="">
                <div class="register-button"> Submit incident reported </div>
            </a>
        </div>
                <br>
        <div class="input-container">
            <a href="exif_clockout.php">
                <div class="register-button"> Log out </div>
            </a>
        </div>

    </div>
</body>
</html>
