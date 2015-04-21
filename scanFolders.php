<?php
session_start();
include_once('recursive.php');

	 if(isset($_POST['inf'])){
	 	
	 	echo $_SESSION['progress'];
	 }else{
	 	$targetFile = realpath('uploads');
	 	$fileOut = new recursiveFile($targetFile,$_SESSION['replace'],$_SESSION['to']);
	 	$fileOut->countFiles();
	 	echo $fileOut->NOF;
		$fileOut->replaceString();
	 }

?>