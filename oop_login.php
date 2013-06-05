<?php  //oop_login.php

	session_start();
	
	if(isset($_SESSION['first_name']) && isset($_SESSION['user_id'])){
		
		header('Location: http://localhost/oop_login/oop_home.php');
	}
	else{
		include('Classes/User.php');
		//if(isset($_POST['submit'])){
		if($_SERVER['REQUEST_METHOD'] == 'POST'){
			
			$object = new User();
			$object->Login();
		}
		
		include('views/oop_login.view.php');
	}

?>
