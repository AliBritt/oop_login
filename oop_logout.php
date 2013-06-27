<?php //oop_logout.php
	
	include('Classes/User.php');
	
	$object = new User();
	$object->Logout();
	
	include('views/oop_loggedOut.view.php')
?>