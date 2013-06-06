<?php	// oop_register.php

include('Classes/User.php');

if($_SERVER['REQUEST_METHOD'] == 'POST'){

	$object = new User();
	$object->Register();
}

include('views/oop_register.view.php');
?>