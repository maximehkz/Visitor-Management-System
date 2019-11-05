<?php
error_reporting(0);

	if(isset($_POST['submit']))
	{
		include_once 'database.php';

		$user_pic = $_FILES['user_pic'];
		$filename = $_FILES['user_pic']['name'];

		$filetmp_name = $_FILES['user_pic']['tmp_name'];
		$fileerror = $_FILES['user_pic']['error'];
		$filesize = $_FILES['user_pic']['size'];
		$filetype = $_FILES['user_pic']['type'];

		$fileExt = explode('.', $filename);
		$fileActualExt = strtolower(end($fileExt));
		$fileAllowed = array('jpg','jpeg','png');

		$video = $_FILES['user_video'];

		$user_video = $_FILES['user_video']['name'];

		$filetmp_name1 = $_FILES['user_video']['tmp_name'];
		$fileerror1 = $_FILES['user_video']['error'];
		$filesize1 = $_FILES['user_video']['size'];
		$filetype1 = $_FILES['user_video']['type'];

		$fileExt1 = explode('.', $user_video);
		$fileActualExt1 = strtolower(end($fileExt1));

		$fileAllowed1 = array('mp4','3gp','ogg','webm','flv','avi');

		$user_comments = $_POST['user_comments'];

        $date = date("Y/m/d");

       if(!empty($user_video))
       {
       	if(in_array($fileActualExt1, $fileAllowed1))
					{
						if($fileerror1 === 0)
						{
							if($filesize1 < 1000000000000000000)
							{
								$randomNumberVideo = rand(1,1000000);
								$fileNewName1 = "video_".$randomNumberVideo.".".$fileActualExt1;
								$fileDestination1 = "uploads/videos/".$fileNewName1;
								move_uploaded_file($filetmp_name1, $fileDestination1);

							}else
							{
								$error =  "File size is to big to upload (max 49 mb)";
							}
						}else
						{
							$error =  "Something goes wrong while uploading video";
						}
					}else
					{
						$error =  "Please select valid video format";
					}
       }
		if(in_array($fileActualExt, $fileAllowed))
		{
			if($fileerror === 0)
			{
				if($filesize < 1000000)
				{
					$randomNumber = rand(1,1000000);
					$fileNewName = "pic_".$randomNumber.".".$fileActualExt;
					$fileDestination = "uploads/".$fileNewName;
					move_uploaded_file($filetmp_name, $fileDestination);


					// video

                    $sql = mysqli_query($conn,"INSERT INTO user_data(user_pic,user_video,user_comments,date) VALUES('$fileNewName','$fileNewName1','$user_comments','$date')");
								if($sql)
								{
									echo '<script type="text/javascript">

                                              window.onload = function () { alert("Data Inserted Successfully"); }

                                    </script>';
								}else
								{
									$error =  "Something goes wrong while inserting data";
								}


				}else
				{
					$error =  "File size is to big to upload";
				}
			}else
			{
				$error =  "Something goes wrong while uploading pic";
			}
		}else
		{
			$error =  "Please select valid image type";
		}

}


 ?>


<!DOCTYPE html>
<html lang="en">
<head>
	<title>My web</title>
	<link rel="stylesheet" type="text/css" href="stylesheet.css">
	<style type="text/css">
	label
	{
	    color: #fff;
	    display: block;
	    margin: 20px 0;
	    font-size: 15px;
	    background: #091280;
	    padding: 8px 15px;
	    text-align: center;
	    cursor: pointer;
	    position:relative;
	}
</style>
</head>
<body style="background: #fff;">

	<div class="form">

		<div style="margin: 10px 0;"><span style="color: red;"><?php echo $error; ?></span></div>

		<form action="mainpage.php" method="POST" enctype="multipart/form-data">
		    <div style="margin-bottom:15px;padding-bottom:20px;text-align:center;"><img src="cisco.jpg"></div>
		    <div style="text-align:center"><button class="incibutton">Incident Reporting</button></div>
			<label>Upload Picture
			<input type="file" name="user_pic" class="input" required id="pic">
			<img src="checkmark-icon-2797531_1280.png" class="tik" id="tik1"></label>
			<label>Upload Video
			<input type="file" name="user_video" class="input" id="video">
				<img src="checkmark-icon-2797531_1280.png" class="tik" id="tik2"></label>
			<!--<textarea name="user_comments" placeholder="Enter your comments" class="input" required >-->
			<!--</textarea>-->
			<div style="position:relative;"><textarea name="user_comments" class="input" id="comment" placeholder="Enter Your Comments"></textarea><img src="checkmark-icon-2797531_1280.png" class="tik" id="tik3"></div>
			<div style="text-align: center;">
				<input type="submit" name="submit" class="submit-btn">
			</div>
		</form>
	</div>


	<script type="text/javascript">
		document.getElementById('pic').onchange = function () {
          document.getElementById('tik1').style.display = "block";
        }
        document.getElementById('video').onchange = function () {
          document.getElementById('tik2').style.display = "block";
        }
         document.getElementById('comment').onchange = function () {
          document.getElementById('tik3').style.display = "block";
        }

	</script>

</body>
</html>
