<?php
$servername="localhost";
$username="root";
$password="";
$dbname="databaseimage";
$conn= mysqli_connect($servername, $username,$password, $dbname);

$id = addslashes( $_REQUEST['id']);

$image=mysqli_query($conn, "SELECT * FROM store  WHERE id=$id");
$image=mysqli_stmt_fetch($image); //getting data from the databse of any field use it for exif
$image=$image['image'];

header("Content-type: image/jpeg");

echo $image;
?>
