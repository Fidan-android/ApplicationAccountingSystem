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

		public function signIn($login, $password) {
			try {
				$query = "SELECT password FROM admin WHERE login=:userlogin";
				$stmt = $this->conn->prepare($query);
				$stmt->execute(array(':userlogin' => $login));
				$response = $stmt->fetch(PDO::FETCH_ASSOC);
				//checking the existence of the user
				if (count($response) == 0) return "Invalid user";
				//verifying the psw is correct
				if (!password_verify($password, $response[0]["hash_password"])) return "Invalid password";
				

			} catch (PDOException $ex) {
				return "Request error: " . $ex->getMessage();
			}
		}
    }