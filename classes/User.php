<?php
	class User{
		private $_db,
				$_data,
				$_sessionName,
				$_cookieName,
				$_isLoggedIn;
		public function __construct($user = null){
			$this->_db = DB::getInstance();
			$this->_sessionName = Config::get("session/session_name");
			$this->_cookieName = Config::get("remember/cookie_name");
			if(!$user){
				if(Session::exists($this->_sessionName)){
					$user = Session::get($this->_sessionName);
					if($this->find($user)){
						$this->_isLoggedIn = true;
					}else{
					}
				}
			}else{
				$this->find($user);
			}
		}
		public function update($fields = array(), $id = null){
			if(!$id && $this->isLoggedIn()){
				$id = $this->data()->user_id;
			}
			if(!$this->_db->update("users", $fields, "user_id", $id)){
				throw new Exception("There was a problem updating data");
			}
		}
		public function create($fields = array()){
				if(!$this->_db->insert("users", $fields)){
				throw new Exception("There was an error");
			}
		}
		public function find($user = null){
			if($user){
				$field = (is_numeric($user))? "user_id" : "username";
				$data = $this->_db->action("SELECT *", "users", array($field, "=", $user));

				if($data->count()){
					$this->_data = $data->first();
					return true;
				}
			}
			return false;
		}
		public function findAll($offset = null){
			if($offset){
				if($offset = "zero"){
					$data = $this->_db->action("SELECT *","users",array("1","=","1"),"","LIMIT 8 OFFSET 0");
				}else{
					$data = $this->_db->action("SELECT *","users",array("1","=","1"),"","LIMIT 8 OFFSET {$offset}");
				}
			}else{
				$data = $this->_db->action("SELECT *","users",array("1","=","1"));
			}
			if($data->count()){
				$this->_data = $data->results();
				return true;
			}
			return false;
		}

		public function guardClockIn(){
			$this->_db->insert("guard_sessions",array(
				'guard_id' => $this->data()->user_id,
				'clock_in_time' => date("Y:m:d h:i:s")
			));
		}

		public function guardClockOut(){
			$this->_db->update("guard_sessions",array(
				'clock_out_time' => date("Y:m:d h:i:s")
			),"guard_id", $this->data()->user_id);
		}

		public function login($username = null, $password = null, $remember = false){

			if(!$username && !$password && $this->exists()){
				Session::put($this->_sessionName, $this->data()->user_id);
			}else{
				$user = $this->find($username);
				if($user){
					if($this->data()->password === Hash::make($password, $this->data()->salt)){
						self::update(array(
							'last_login' => date("Y-m-d h:i:a")
						),$this->data()->user_id);
						Session::put($this->_sessionName, $this->data()->user_id);
						if($remember){
							$hash = Hash::unique();
							$hashCheck = $this->_db->action("SELECT *", "users_session", array("user_id", "=", $this->data()->user_id));

							if(!$hashCheck->count()){

								$this->_db->insert("users_session",array(
									'user_id' => $this->data()->user_id,
									'hash' => $hash
								));
							}else{
								$hash = $hashCheck->first()->hash;
							}
							Cookie::put($this->_cookieName, $hash, Config::get("remember/cookie_expiry"));
						}
						if($this->data()->type == "user"){
							return "user";
						}else if($this->data()->type == "guard"){
							return "guard";
						}else if($this->data()->type == "host"){
							return "host";
						}else{
							return "error";
						}
					}
				}
			}

			return false;
		}
		public function delete($user = null){
			return $this->_db->delete("users", array("user_id","=", $user->user_id));
		}
		public function exists(){
			return (!empty($this->_data)) ? true: false;
		}
		public function logout(){
			$this->_db->delete("users_session",array("user_id","=",$this->data()->user_id));
			Session::delete($this->_sessionName);
			Cookie::delete($this->_cookieName);
		}
		public function data(){
			return $this->_data;
		}
		public function isLoggedIn(){
			return $this->_isLoggedIn;
		}
	}
?>
