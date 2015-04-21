<?php
	session_start();
	if(isset($_POST['replace']) && isset($_POST['to'])){
		$_SESSION['replace'] = $_POST['replace'];
		$_SESSION['to'] = $_POST['to'];
		echo "true";
		exit();
	}
	echo "false";
?>