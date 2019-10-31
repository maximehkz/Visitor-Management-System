<?php
	session_start();
	$GLOBALS["config"] = array(
		"mysql" => array(
			"host" => "localhost",
			"username" => "root",
			"password" => "",
			"db" => "aegis_new" //add database name once created
		),
		"remember" => array(
			"cookie_name" => "hash",
			"cookie_expiry" => 604800
		),
		"session" => array(
			"session_name" => "username",
			"token_name" => "token"
		),
		"variables" => array(
			"activePage" => basename($_SERVER['PHP_SELF'],".php")
		)
	);
	spl_autoload_register(function($class){
		require_once "classes/".$class.".php";
	});
	require_once "functions/sanitize.php";
	date_default_timezone_set("Asia/Kuala_Lumpur");
	if(Cookie::exists(Config::get("remember/cookie_name")) && !Session::exists(Config::get("session/session_name"))){
		$hash = Cookie::get(Config::get("remember/cookie_name"));
		$hashCheck = DB::getInstance()->action("SELECT *" ,"users_session", array("hash", "=", $hash));
		if($hashCheck->count()){
			$user = new User($hashCheck->first()->user_id);
			$user->login();
		}
	}
?>
