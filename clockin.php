<?php
    $visitor = json_decode($_POST["visitor"]);
    $name = $visitor->Name;
    $visitorName = $visitor->visitorName;
    $address = $visitor->address;
    $phone = $visitor->phone;
    $email = $visitor->email;
    $nic = $visitor->nic;

?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
    <head>
        <meta charset="utf-8">
        <title>Clock In</title>
        <link rel = "stylesheet" href="./css/register.css">
        <link rel = "stylesheet" href="./css/reset.css">
        <link rel = "stylesheet" href="./css/global.css">
    </head>
    <body>
        <div class="main-container clockin-container">
            <?php require_once("header.php") ?>
            <form action="clockVisitor.php" method="POST">
                <div class="field-container">
                    <div class="field-name">Host Name</div>
                    <div class="field-value">
                        <input type="text" value="<?php echo $name?  $name : "" ?>" disabled>
                    </div>
                </div>
                <div class="field-container">
                    <div class="field-name">Phone No</div>
                    <div class="field-value">
                        <input type="number" value="<?php echo $phone?  $phone : ""?>" disabled>
                    </div>
                </div>
                <div class="field-container">
                    <div class="field-name">Visitor Name</div>
                    <div class="field-value">
                        <input type="text" value="<?php echo $visitorName?  $visitorName : "" ?>" disabled>
                    </div>
                </div>
                <div class="field-container">
                    <div class="field-name">NIC</div>
                    <div class="field-value">
                        <input id="nic" type="text" name="nic" value="<?php echo $nic?  $nic: "" ?>" disabled>
                    </div>
                </div>
                <div class="field-container">
                    <div class="field-name">E-mail</div>
                    <div class="field-value">
                        <input type="text" value="<?php echo $email?  $email : "" ?>" disabled>
                    </div>
                </div>
                <input type="hidden" name="nic" value="<?php echo $nic ?>">
                <input type="hidden" name="action" value="clockin">
                <button class="clockin-button" type="submit">
                    Clock In
                </button>
            </form>
        </div>
        <script src="./js/jquery-1.10.2.min.js"></script>
        <script src="./js/jquery-ui.js"></script>
    </body>
</html>
