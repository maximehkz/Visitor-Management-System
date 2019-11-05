<?php

	require_once("core/init.php");
	if(Input::exists()){
		if(Token::check(Input::get("token"))){
			$validate = new Validate();
			$validation = $validate->check($_POST,array(
				'username' => array('required' => true),
				'password' => array('required' => true)
			));
			if($validation->passed()){
				$user = new User();
				$remember = (Input::get("remember_me") === "on"? true: false);
				$login = $user->login(Input::get("username"), Input::get("password"), $remember);

				if($login == "user"){
					Redirect::to("photoauthin.php");
				}else if($login == "guard"){
					Redirect::to("exif_clockin.php");
				}else if($login == "host"){
					Redirect::to("dashboardhost.php");
				}else{
					echo "<p>Unable to login.</p>";
				}
			}else{
				foreach($validation->errors() as $error){
					echo $error, "<br>";
				}
			}
		}
	}
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
	<meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <link rel="stylesheet" href="./css/register.scss">
    <link rel="stylesheet" href="./css/reset.css">
    <link rel="stylesheet" href="./css/global.scss">
    <title>Login</title>
</head>
<body>
    <div class="main-container">
        <?php require_once("header.php"); ?>
		<form action="" method="post">
            <div class="input-container">
			<div class="input-icon-container">
            <img class="login-icon" src="./image/male.png" >
			</div>
            <input type="text" name="username">
            </div>

            <div class="input-container">
            <img style=" align-content: justify; height:50px; width:50px;" src="./image/lock.png">
                <input type="password" name="password">
            </div>

			<input type="hidden" name="token" value="<?php echo Token::generate();?>">
            <div class="input-container">
    		<button class="register-button">Login </button	>
            </div>
        </form>
    </div>
</body>
</html>
