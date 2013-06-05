<?php

include('Classes/User.php');

if($_SERVER['REQUEST_METHOD'] == 'POST'){

	$object = new User();
	$object->Change_pass();
}

include('views/oop_change_pass.view.php');
?>