<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <link rel="stylesheet" href="./css/register.scss">
    <link rel="stylesheet" href="./css/reset.css">
    <link rel="stylesheet" href="./css/global.scss">
    <title>EXIF-clock in PA</title>
</head>
<body>
    <div class="main-container">
        <?php require_once("header.php"); ?>
        <div class="input-container">
        <text style="font-size:20px; font-weight:bold;"> Clock in Guard: </text>
        </div>
        <br>
        <div class="input-container">
        <text> Please take a picture: </text>
        </div>
            <div class="input-container">
                <form action="upload-clockin.php" method="POST" enctype="multipart/form-data">
                    <img src="./image/cameraicon.png" class="center" onclick="document.getElementById('imageUpload').click()"></img>
                    <input type="file" name="image" accept="image/*" id="imageUpload" alt="Submit" style="display:none">
                    <input class="buttonsub-style" type="submit" name="submit_image" value="Clock in">
                </form>
            </div>
<!-- pass id to the validation controller
// get image using the id and then use exif to get the date and time
// make ajax requerst to a server to get current date and time
// then match to do clock in and clock out
//if clock in correct direct to page exif_correct
//if clock in wrong direct to page exif_wrong -->

    </div>
</body>
</html>
