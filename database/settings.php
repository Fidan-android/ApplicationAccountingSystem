<?php
    class Database {

		private $conn;

		public function __construct($host, $db_name, $db_user, $db_password) {
			$this->conn = null;
			try {
				$this->conn = new PDO("mysql:host=$host; dbname=$db_name", 
					$db_user, 
					$db_password, 
					array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET lc_time_names = 'ru_RU';")); 
				$this->conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
			}  catch(PDOException $e) {   
				echo "Exception: " . $e->getMessage();
			}
    	} 

    	public function getConnection() {
			return $this->conn;
		}

		//function for logging in to your account 
		public function signIn($db_prefix, $login, $password) {
			try {
				$query = "SELECT hash_password FROM " . $db_prefix . "auth WHERE login=:userlogin";
				$stmt = $this->conn->prepare($query);
				$stmt->execute(array(':userlogin' => $login));
				$response = $stmt->fetch(PDO::FETCH_ASSOC);
				//checking the existence of the user
				if ($response == null) return "error";
				//verifying the psw is correct
				if (!password_verify($password, $response[0]["hash_password"])) return "error";
				else return "success";
			} catch (PDOException $ex) {
				return $ex->getMessage();
			}
		}

		//function for creating an account 
		public function signUp($db_prefix, $login, $password, $phone, $firstname, $middlename) {
			try {
				$query = "INSERT INTO " . $db_prefix . "auth (login, hash_password, phone, firstname, middlename)
							VALUES(:login, :password, :phone, :firstname, :middlename)";
				$stmt = $this->conn->prepare($query);
				$response = $stmt->execute(array(":login" => $login, ":password" => $password, ":phone" => $phone, ":firstname" => $firstname, ":middlename" => $middlename));
				if ($response)
					return "success";
				else
					return "error";
			} catch(PDOException $ex) {
				return "request error: " . $ex->getMessage();
			}
		}

		public function saveToken($login, $token) {
			try {
				$query = "INSERT INTO " . $db_prefix . "tokens (login, token)
							VALUES(:login, :token)";
				$stmt = $this->conn->prepare($query);
				$response = $stmt->execute(array(":login" => $login, ":token" => $token));
				if ($response)
					return "success";
				else 
					return "error";
			} catch(PDOException $ex) {	
				return "request error: " . $ex->getMessage();
			}
		}
    }