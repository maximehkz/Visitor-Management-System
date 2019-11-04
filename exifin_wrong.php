<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <link rel = "stylesheet" href="./css/register.css">
    <link rel = "stylesheet" href="./css/reset.css">
    <link rel = "stylesheet" href="./css/global.css">
        <link rel = "stylesheet" href="./css/exif.css">
    <title>Preregister Visitor</title>
</head>
<body>
    <div class="main-container">
        <?php require_once("header.php"); ?>

            <div class="input-container">

                <img src="./image/unlock.png" class="center"></img>
                <br>
                <text style="font-size:20px;"> You have not been succesfully clocked in ! </text>
            </div>

            <br>

            <div class="input-container">
                <table>
                    <tr>
                    <td>  <text class="text-style"> Date: </text> </td>  <td> <img class="icon-style" src="./image/wrongicon.png"> </td>
                    </tr>
                    <tr>
                    <td>  <text class="text-style"> Time: </text> </td>  <td> <img class="icon-style" src="./image/wrongicon.png"> </td>
                    </tr>

            </table>
                </a>
            </div>

            <br>
            <div class="input-container">
                <a href="exif_clockin.php">
    				<div class="register-button"> Try again </div>
    			</a>
            </div>

    </div>
</body>
</html>
