<?php
//check if session is set
	session_start();
	
	if(isset($_SESSION['first_name'])&& isset($_SESSION['user_id'])){
		
		$first_name = $_SESSION['first_name'];
		
		include('views/oop_home.view.php');
	
	}
	else{
		header('Location: http://localhost/oop_login/oop_login.php');
	}
	
	
?>