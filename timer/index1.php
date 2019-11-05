<?php
$servername="localhost";
$username="root";
$password="";
$dbname="aegis_new";
$link= mysqli_connect($servername, $username,$password, $dbname);
?>
<div id="response"> </div>

<script type="text/javascript">
setInterval(function()
{
    var xmlhttp=new XMLHttpRequest();
    xmlhttp.open("GET","response.php",false);
    xmlhtp.send(null);
    document.getElementByID("response").innerHTML=xmlhttp.responseText;

},1000);
</script>
