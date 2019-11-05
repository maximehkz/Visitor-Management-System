<?php 
    error_reporting(0);
	include_once 'database.php';

     if (isset($_POST['update'])) {
	$id = $_POST['id'];
	$status = $_POST['status'];
	$sql = mysqli_query($conn, "UPDATE user_data SET STATUS = '$status' WHERE id='$id'");
	if($sql)
	{
		$error = "Status update suuccessfully";
	}else
	{
		$error =  "Error in updating status";
	}
}



	$sql =  "SELECT * FROM user_data";
	$result = mysqli_query($conn, $sql);



 ?>
<!DOCTYPE html>
<html>
<head>
	<title>Admin</title>
	<link rel="stylesheet" type="text/css" href="stylesheet.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
</head>
<body>

	<div style="text-align:center;margin-top:100px;">
	    <div style="margin-top: -20px;margin-bottom: 20px;"><span><?php echo $error; ?></span></div>
	    <div style="margin-bottom:15px;"><img src="cisco.jpg"></div>
	    <button class="incibutton">Incidents Reported</button>
	    <div class="reports">
	    	<ul>
	    		
	 
	<?php 
	if(mysqli_num_rows($result) > 0)
	{
		while($row = mysqli_fetch_assoc($result))
		{
			$datas = $row;
		    echo '<p class="caption">Report '.$row['id'].'</p>
		            <li>
		                
		                <div class="dropdown">
            				<div class="drop-content">
            			    	<h4 style="padding-top:19px;padding-left: 37px;color: #403c3c;text-align:left;">About the Report</h4>
                                <div class="main">
                					<div class="image">
                					    <span>Image</span>
                						<img src="uploads/'.$row['user_pic'].'" class="image" style="width:50px;">
                					</div>
                					<div class="video">
                					    <span>Video</span>
                						<video controls class="video" style="width:68px">
                                			<source src="uploads/videos/'.$row['user_video'].'" type="video/mp4">
                                		</video>
                					</div>
                				</div>
                				<div class="text-cont">
                					
                					<div style="margin-top: 26px;text-align: left; padding-left: 38px;">&nbsp; </div>
                					 <div class="main">
                					<div class="image">
                					    <span style="margin-left: 31px;">Date</span>
                					    <div>'.$row['date'].'</div>
                					</div>
                					<div class="video">
                					     <span style="margin-left: -51px;">Status</span>
                					    <div>'.$row['STATUS'].'</div>
                					</div>
                				</div>
                				</div>
                				<div style="margin-top: 28px;text-align: left;padding-left: 39px;">
                				    <span>'.$row['user_comments'].'</span>
                				</div>
                					<div class="status">
                                	    <form action="reports.php" method="POST">
                                	        <input type="hidden" value="'.$row['id'].'" name="id" >
                                	        <label for="yes">Yes</label><input type="radio" name="status" id="yes" value="Yes">
                                	        <label for="No">No</label><input type="radio" name="status" id="No"  value="No">
                                	        <input type="submit" value="Update" name="update">
                                	    </form>
                                	</div>
            				</div>
            			</div>
		            </li>
		        
		        ';
		}
	}
	?>
		 	</ul>
	    </div>
	</div>
	<div style="text-align:center">
	    <h2
	</div>

	<script type="text/javascript">
		$(document).ready(function(){
		  $("li").click(function(){
		    $(".dropdown", this).show(10);
		  });
		  $(".caption").click(function(){
		    $(".dropdown").hide(10);
		  });
		});
	</script>

</body>
</html>