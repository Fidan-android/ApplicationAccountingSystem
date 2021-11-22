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
    }