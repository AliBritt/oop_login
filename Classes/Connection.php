<?php  // connection.php

require '/var/PDOlogininfoConnection.php';

class Connection{
	
	private $db_host = DB_HOST;
	private $db_name = DB_NAME;
	
	public function dbConnect(){
		
		return new PDO("mysql:host=$this->db_host; dbname=$this->db_name", DB_USER, DB_PASS);
		
	}
}


?>