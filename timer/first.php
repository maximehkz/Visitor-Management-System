<?php
$servername="localhost";
$username="root";
$password="";
$dbname="aegis_new";
$link= mysqli_connect($servername, $username,$password, $dbname);
$duration="";
mysqli_select_db($link,"aegis_new");
$res=mysqli_query($link,"SELECT * FROM timertable");
while($row=mysqli_fecth_array($res) )
{
$duration=$row["duration"];
}

$_SESSION["duration"]=$duration;
$_SESSION["start_time"]=date("Y-m-d H:i:s");

$end_time=$end_time=date('Y-m-d H:i:s', strtotime('+'.$_SESSION["duration"].'minutes', strtotime($_SESSION["start_time"])));

$_SESSION["end_time"]=$end_time;

?>
<script type="text/javascript">
window.location="index1.php";
</script>
