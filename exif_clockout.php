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
                <text style="font-size:20px;"> Clock out Guard: </text>
                </div>
                <br>
            <div class="input-container">
                <form method="POST" enctype="multipart/form-data">
                <img src="./image/cameraicon.png" class="center" onclick="document.getElementById('imageUpload').click()"></img>
                <input type="file" name="image" accept="image/*" id="imageUpload" alt="Submit" style="display:none" capture >
                 <input class="register-button" type="submit" name="submit_image" value="Upload">
            </div>
            </form>

<?php
//connection to database
$servername="localhost";
$username="root";
$password="";
$dbname="databaseimage";
$conn= mysqli_connect($servername, $username,$password, $dbname);

//check connection
if(!$conn) {
    die("Connection failed:".mysqli_close($conn));
}

//file properties
if(isset($_FILES['image'])){
    $file = $_FILES['image'] ['tmp_name'];
}

if (!isset($file))
{
    echo"Please Take a picture.";
}
else
{
$image = addslashes ($_FILES['image'] ['tmp_name']);
$image_name= addslashes ($_FILES['image'] ['name']);

if (!$image)
{
      echo "That's not an image.";
}
else
    {
        if (!mysqli_query ($conn, "INSERT INTO store  VALUES ('', '$image_name','$image')"))
        {
            echo "Problem uploading image.";
        }
        else
        {
            $lastid=mysqli_insert_id($conn);
            echo "Image has been succesfully stored!";
        //    echo"Image Uploaded.<p/> Your image: <p/> <img src=getdata.php? id=$lastid>";
        }
    }
}

?>
            <div class="input-container">
                <a href="exifout_correct.php"> <!-- pass id to the validation controller
                    // get image using the id and then use exif to get the date and time
                    // make ajax requerst to a server to get current date and time
                    // then match to do clock in and clock out
                    //if clock out correct direct to page exifout_correct
                    //if clock out wrong direct to page exif_clockout -->
    				<div class="register-button"> Photo Authentication </div>
    			</a>
            </div>

    </div>
</body>
</html>
