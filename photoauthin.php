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
        <form  action="exif_clockin.php" method="post" target="_self">

            <div class="input-container">
            <text name="dateshift"> Time shift start: </text>  <br>
            </div>

            <div class="input-container">
                <input type="text" name="time" value="" required>
            </div>

            <div class="input-container">
            <text name="dateshift"> Date of shift: </text>  <br>
            </div>

            <div class="input-container">
                <input type="date" name="Date" value="" required>
            </div>

            <div class="input-container">
                <input type="submit" class="register-button" value="Proceed to PA"></input>
            </div>
        </form>
    </div>
</body>
</html>
