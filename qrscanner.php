<!DOCTYPE html>
<html lang="en" dir="ltr">
    <head>
        <meta charset="utf-8">
        <title> QR Scanner </title>
        <meta name="viewport" content="width=device-width, initial-scale=1"/>
        <link rel="stylesheet" href="./css/register.scss">
        <link rel="stylesheet" href="./css/reset.css">
        <link rel="stylesheet" href="./css/global.scss">
        <link rel="stylesheet" href="./css/exif.css">
    </head>
    <body>
        <div class="main-container qrscanner-container">
            <?php require_once("header.php"); ?>
            <div class="qr-container">
                <img src="./image/ciscoQR.png" alt="qrcode">
            </div>
            <label class="scanQR-button">
                Open Scanner
                <input type=file accept="image/*" capture=environment onchange="openQRCamera(this);" tabindex=-1>
            </label>
            <form id="hidden-form" action="clockin.php" method="post">
                <input id="hidden-input" type="hidden" name="visitor" value="">
            </form>
        </div>

        <script src="./js/qrscanner.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
        <script>
        function openQRCamera(node) {
            var reader = new FileReader();
            reader.onload = function() {
                node.value = "";
                qrcode.callback = function(res) {
                    if(res instanceof Error) {
                        alert("No QR code found. Please make sure the QR code is within the camera's frame and try again.");
                    } else {
                        document.getElementById("hidden-input").value = res;
                        document.getElementById("hidden-form").submit();
                    }
                };
                qrcode.decode(reader.result);
            };
            reader.readAsDataURL(node.files[0]);
        }


        </script>
    </body>
</html>
