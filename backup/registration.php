<?php

	session_start();
	$username = "";
	$email = "";
	$errors = array();
	require("./global/server.php");
	//Checks if "register_user" is POST-ed
	if(isset($_POST["register_user"])){
		//Receive input from form and initialize variables
		$username = $_POST["username"];
		$email = $_POST["email"];
		$password_1 = $_POST["password-1"];
		$password_2 = $_POST["password-2"];
		// Form validation
		if(empty($username)){
			array_push($errors, "Username must be filled");
		}
		if(empty($email)){
			array_push($errors, "Email must be filled");
		}
		if(empty($password_1)){
			array_push($errors, "Password must be filled");
		}
		if(empty($password_2)){
			array_push($errors, "Please confirm password");
		}
		if($password_1 != $password_2){
			array_push($errors, "Passwords do not match");
		}
		$check_username = "SELECT * FROM registration WHERE username='$username' OR email='$email' LIMIT 1";
		$result = $connect->prepare($check_username);
		$result->execute();
		$user = $result->fetch(PDO::FETCH_ASSOC);
		// if user exists
		if($user){
			if($user['username'] === $username){
				array_push($errors,"Username already exists");
			}
			if($user['email'] === $email){
				array_push($errors, "There is already an account with this email address");
			}
		}
		if(count($errors) == 0){
			$password = md5($password_1);
			$query = "INSERT INTO registration (username, email, password, type) VALUES ('$username','$email','$password','user')";
			$result = $connect->prepare($query);
			$result->execute();
			$_SESSION['username'] = $username;
			header("location: index.php");
		}
	}

	//Checks if "login_user" is POST-ed
	if(isset($_POST["login_user"])){
		$username = $_POST["username"];
		$password = $_POST["password"];
		if(empty($username)){
			array_push($errors, "Username is required");
		}
		if(empty($password)){
			array_push($errors, "Password is required");
		}
		if(count($errors) == 0){
			$password = md5($password);
			$query = "SELECT * FROM registration WHERE username='$username' AND password='$password'";
			$result = $connect->prepare($query);
			$result->execute();
			$login = $result->fetch(PDO::FETCH_ASSOC);
			if(count($result) == 1){
				if($login["type"] == "user"){
					$_SESSION["username"] = $username;
					header("location: index.php");
				}else if($login["type"] == "admin"){
					$_SESSION["username"] = $username;
					header("location: dashboard.php");
				}
			}else{
				array_push($errors, "Wrong username/password combination");
			}
		}
	}
?>
