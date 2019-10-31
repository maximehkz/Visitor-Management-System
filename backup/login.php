<?php

	require_once("Core\init.php");
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
					Redirect::to("index.php");
				}else if($login == "admin"){
					Redirect::to("dashboard.php");
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
<html>
<head>
	<title>Login | EatWhat</title>
	<link rel="stylesheet" type="text/css" href="./css/reset.css">
	<link rel="stylesheet" type="text/css" href="./css/global.css">
	<link rel="stylesheet" type="text/css" href="./css/registration.css">
	<link href="https://fonts.googleapis.com/css?family=Raleway" rel="stylesheet">
	<meta name="viewport" content="width=device-width, initial-scale=1">

</head>
<body>
	<div id="login-wrapper">
		<h1 class="form-title">Sign In</h1>
		<div id="form-container">
			<form id="login-form" action="" method="POST">
				<div class="user-input-area">
					<div class="icon-label">
						<label for="username"><img src="./img/user-icon.png" alt="user-icon.png"></label>
					</div>
					<input class="user-input" id="username" type="text" name="username" value="<?php echo escape(Input::get("username"));?>" autocomplete="off" placeholder="Username">
					<span class="line-section"></span>
				</div>
				<div class="user-input-area">
					<label class="icon-label" for="password"><img src="./img/password-icon.png" alt="password-icon.png"></label>
					<input class="user-input" id="password" type="password" name="password" autocomplete="off" placeholder="Password">
					<span class="line-section"></span>
				</div>
				<input type="hidden" name="token" value="<?php echo Token::generate();?>">
				<div class="user-input-area" id="remember-checkbox">
					<label for="remember" id="rememberCheckbox">
						<input type="checkbox" name="remember_me" id="remember">Remember me
					</label>
				</div>
				<div class="user-input-area">
					<button class="button login-button" type="submit"><img src="./img/telegram-plane.png" alt="login"></button>
				</div>
				<div class="user-input-area">
					<h1 class="register-link"><a href="sign_up.php">New user? Sign up here</a></h1>
				</div>
			</form>

		</div>
	</div>
</body>
</html>
