<?php
session_start();
include_once("recursive.php");
$ds          = DIRECTORY_SEPARATOR;  // Se obtiene el separador de directorios
$storeFolder = '';   // ruta a la carpeta donde sera almacenado el archivo descomprimido
$zip = new ZipArchive;
 
if (!empty($_FILES)) {

	// Carga del archivo
    $tempFile = $_FILES['file']['tmp_name']; 
    $targetPath = dirname( __FILE__ ) . $ds. $storeFolder .$ds ;
    $targetFile =  $targetPath. $_FILES['file']['name'];
    $filename   = $_FILES['file']['name'];
    move_uploaded_file($tempFile,$targetFile);
    // Fin de la carga del Archivo
    // Descompresión del zip cargado
    $zipFile = $targetFile;
    if ($zip->open($targetFile) === TRUE) {
    	$targetFile = substr($targetFile, 0,-4);
    	if(mkdir($targetFile))
	    	$zip->extractTo($targetFile);
	    $zip->close();
	// Fin de la descompresión
	    unlink($zipFile); // Se borra el archivo zip
	    // Se intancia la clase reursive
	    $fileOut = new recursiveFile($targetFile,$_SESSION['replace'],$_SESSION['to'],$storeFolder,$filename); 
	    $fileOut->replaceString(); // Se hace el llamado a reemplazar las cadenas de texto
	    $fileOut->downloadNewZip(); // Se descarga el archivo zip nuevo
	} 
}

?> 