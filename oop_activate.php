<?php	//activate.php

include('Classes/User.php');

if (isset($_GET['x'])&& isset($_GET['y'])) {
	
	$object = new user();
	$object->Activate();
}

?>