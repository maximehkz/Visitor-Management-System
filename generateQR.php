<?php

require_once("Core/init.php");

if(Input::exists()){

    $qr = new QRController();
    
    try{

         $qr->create(array(
             'host_name'     => Input::get("host_name"),
             'visitor_name'   => Input::get("visitor_name"),
             'address'       => Input::get("address"),
             'phone'         => Input::get("phone"),
             'email'         => Input::get("email"),
             'nic'           => Input::get("nic"),
             'created_at'    => date("Y-m-d h:i:sa")));

        $qr->generateQRCode(Input::get("host_name"),
                            Input::get("visitor_name"),
                            Input::get("address"),
                            Input::get("phone"),
                            Input::get("email"),
                            Input::get("nic"));

    }catch(Exception $e){
        die($e->getMessage());
    }

}

?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
    <head>
        <meta charset="utf-8">
        <title>QRCode</title>
        <link rel="stylesheet" href="./css/register.css">
        <link rel="stylesheet" href="./css/global.css">
        <link rel="stylesheet" href="./css/reset.css">
    </head>
    <body>
        <div class="main-container">
            <?php require_once("header.php") ?>
            <h1 class="qr-text">Take a screenshot of QRCode and send it to the visitor</h1>
            <div class="qr-container">
                <img src="./qrcode/temp/<?php echo $qr->data();?>.png" alt="qrcode">
            </div>
        </div>
    </body>
</html>
