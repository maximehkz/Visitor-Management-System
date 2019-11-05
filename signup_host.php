<?php
	require_once("core/init.php");
	if(Input::exists()){

		if(Token::check(Input::get("token"))){
			$validate = new Validate();
			$validation = $validate->check($_POST, array(
                'fullname' => array(
                    'required' => true,
                    'min' => 2,
                    'max' => 100,
                ),
                'email' => array(
                    'required' => true,
                    'min' => 2,
                    'max' => 100,
                ),
                'icnumber' => array(
                    'required' => true,
                    'min' => 2,
                    'max' => 100,
                ),
                'phonenumber' => array(
                    'required' => true,
                    'min' => 2,
                    'max' => 100,
                ),
                'username' => array(
                    'required' => true,
                    'min' => 2,
                    'max' => 100,
                    'unique' => 'users'
                ),
				'password' => array(
					'required' => true,
					'min' => 6
				),
				'confirmpassword' => array(
					'required' => true,
					'matches' => 'password'
				)
			));
			if($validation->passed()){

				$user = new User();
				$salt = Hash::salt(10);

				try{
					$user->create(array(
                        'fullname' => Input::get("fullname"),
						'email' => Input::get("email"),
                        'ic_number' => Input::get("icnumber"),
                        'phone_number' => Input::get("phonenumber"),
                        'username' => Input::get("username"),
						'password' => Hash::make(Input::get("password"), $salt),
						'salt' => $salt,
						'created_at' => date("Y-m-d h:i:sa"),
						'type' => "host"
					));
					Session::flash("home","Welcome,".Input::get("username")."!");
					Session::put("username",Input::get("username"));
					Redirect::to("login.php");
				}catch(Exception $e){
					die($e->getMessage());
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
	<link rel="stylesheet" href="./css/exif.css">
    <title>Preregister Visitor</title>
</head>
<body>
    <div class="main-container">
        <?php require_once("header.php"); ?>
        <form action="" method="post">
            <div class="input-container">
                <h1>Full name:</h1>
                <input type="text" name="fullname">
            </div>
            <div class="input-container">
                <h1>E-mail Address:</h1>
                <input type="text" name="email">
            </div>
            <div class="input-container">
                <h1>IC Number:</h1>
                <input type="text" name="icnumber">
            </div>
            <div class="input-container">
                <h1>Phone Number:</h1>
                <input type="text" name="phonenumber">
            </div>
            <div class="input-container">
                <h1>Username:</h1>
                <input type="text" name="username">
            </div>
            <div class="input-container">
                <h1>Password:</h1>
                <input type="password" name="password">
            </div>
            <div class="input-container">
                <h1> Confirm Password:</h1>
                <input type="password" name="confirmpassword">
            </div>
			<input type="hidden" name="token" value="<?php echo Token::generate(); ?>">
            <div class="input-container">
                <button type="submit" class="register-button">Sign up</button>
            </div>
        </form>
    </div>
</body>
</html>
