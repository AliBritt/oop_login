<?php // forgot_pass.php
include('Classes/User.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST'){

	$object = new User();
	$object->Forgot_pass();
}

include('views/oop_forgot_pass.view.php');
?>