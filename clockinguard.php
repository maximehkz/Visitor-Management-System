<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <link rel = "stylesheet" href="./css/register.css">
    <link rel = "stylesheet" href="./css/reset.css">
    <link rel = "stylesheet" href="./css/global.css">
    <title>Preregister Visitor</title>
</head>
<body>
    <div class="main-container">
        <?php require_once("header.php"); ?>

            <div class="input-container">
                <img src="./image/cameraicon.png" class="center" onclick="document.getElementById('imageUpload').click()"></img>
                <input type="file" id="imageUpload" alt="Submit" style="display:none" >
            </div>

            <div class="input-container">
                <a href="">
    				<div class="register-button">Clock in </div>
    			</a>
            </div>

    </div>
</body>
</html>
