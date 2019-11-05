<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <link rel="stylesheet" href="./css/register.scss">
    <link rel="stylesheet" href="./css/reset.css">
    <link rel="stylesheet" href="./css/global.scss">
    <link rel="stylesheet" href="./css/exif.css">
    <title>Preregister Visitor</title>
</head>
<body>
    <div class="main-container">
        <?php require_once("header.php"); ?>
        <form action="generateQR.php" method="post">
            <div class="input-container">
                <h1>Host Name</h1>
                <input type="text" name="host_name" required>
            </div>
            <div class="input-container">
                <h1>Visitor Name</h1>
                <input type="text" name="visitor_name" required>
            </div>
            <div class="input-container">
                <h1>Host Address</h1>
                <input type="text" name="address" required>
            </div>
            <div class="input-container">
                <h1>Host Phone Number</h1>
                <input type="number" name="phone" required>
            </div>
            <div class="input-container">
                <h1>Host E-mail</h1>
                <input type="email" name="email" required>
            </div>
            <div class="input-container">
                <h1> Visitor NIC/Passport</h1>
                <input type="text" name="nic" required>
            </div>
            <div class="input-container">
                <button type="submit" class="register-button">Register</button>
            </div>
        </form>
    </div>
</body>
</html>
