<?php
/**
*	@autor Michael Sánchez
*   @Version 1.8.2
*	@date 20/04/2015
*	
*	Esta clase contiene las funciones encargadas de recorrer, reemplazar,
*	desempaquetaar, empaquetar y descargar los zip de las unidades 
*	defectuosas.
*   _________________________________________________________
*	(       )\__   __/(  ____ \|\     /|(  ___  )(  ____ \( \      
*	| () () |   ) (   | (    \/| )   ( || (   ) || (    \/| (      
*	| || || |   | |   | |      | (___) || (___) || (__    | |      
*	| |(_)| |   | |   | |      |  ___  ||  ___  ||  __)   | |      
*	| |   | |   | |   | |      | (   ) || (   ) || (      | |      
*	| )   ( |___) (___| (____/\| )   ( || )   ( || (____/\| (____/\
*	|/     \|\_______/(_______/|/     \||/     \|(_______/(_______/
*	                                                               
*	 _______  _______  _        _______           _______  _______ 
*	(  ____ \(  ___  )( (    /|(  ____ \|\     /|(  ____ \/ ___   )
*	| (    \/| (   ) ||  \  ( || (    \/| )   ( || (    \/\/   )  |
*	| (_____ | (___) ||   \ | || |      | (___) || (__        /   )
*	(_____  )|  ___  || (\ \) || |      |  ___  ||  __)      /   / 
*	      ) || (   ) || | \   || |      | (   ) || (        /   /  
*	/\____) || )   ( || )  \  || (____/\| )   ( || (____/\ /   (_/\
*	\_______)|/     \||/    )_)(_______/|/     \|(_______/(_______/
*
*	Se usan dos modalidades:
* 	1. Carga de archivos zip mediante la interfaz web
*		Al momento de cargar el archivo este se descoprime se iteran los 
*		archivos de la carpeta y se reemplazan,las coincidencias 
*		correspondientes por la cadena de texto indicada, se genera un 
*		nuevo archivo comprimido y se descarga automaticamente.
*
*	2. Carga de carpetas sin comprimir a la raiz del código en la carpeta
*		uploads.Cuando se haya copiado y pegado las respectivas carpetas 
*		con los archivos a traves de la interfaz web se debe dar click 
*		sobre el boton que indica que se recorrera el directorio con las 
*		carpertas el sistema carga estas carpetas y realiza la acción 
*		reemplazo dentro de las carpetas, el usuario debe copiar y pegar 
*		el contenido.
*
*	
**/
class recursiveFile{

	public $folderUrl;
	public $replace;
	public $newText;
	public $storeFolder;
	public $filename;
	public $relativeFilePath;
	public $NOF = 0;
	public $progress = 0;
	/**
	 * [__construct Constructor de la clase recursiveFile]
	 * @param string $folderUrl   Ruta a la carpeta que contiene los arcivos
	 * @param string $replace     Cadena de texto que se va a reemplazar
	 * @param string $newText     Cadena de texto que reemplara la anterior
	 * @param string $storeFolder Carpeta donde se almacenara el zip generado
	 * @param string $filename    Nombre del archivo
	 */
	public function __construct($folderUrl = "",$replace = "",$newText="",$storeFolder = "",$filename=""){
		$this->folderUrl   = $folderUrl;
		$this->replace     = $replace;
		$this->newText     = $newText;
		$this->storeFolder = $storeFolder;
		$this->filename    = $filename;		
	}
	/**
	 * [replaceString Funcion encargada de reemplazar las cadenas de texto]
	 * @return [No retorna valores]
	 */
	public function replaceString(){
		$di = new RecursiveDirectoryIterator($this->folderUrl); // Se crea una iteración del directorio
		$count = 0; // Declaración de la variable que llevara el conteo de los archivos procesados
		foreach (new RecursiveIteratorIterator($di) as $filename => $file) { // Comienzo del ciclo que recorrera los archivos
			$count++; // Se aumenta el contador
			$temExt= explode(".", $filename); // Se crea un array desde el nombre del archivo
			$validExtensions = array("html"); // Array con las extenciones que seran validadas 
			if(in_array(end($temExt), $validExtensions)){ // Si la extencion va a ser validada
				$path_to_file = $filename; // Se declara variable con la ruta al archivo
				$file_contents = file_get_contents($path_to_file); // Se obtiene el archivo
				$quotesArray = explode('"', $file_contents); // Se separa el archivo por comillas (quotes)
				foreach ($quotesArray as $key) { // Se recorre cada sección del array generado
					if(base64_encode(base64_decode($key)) === $key){ // Si este texto esta cifrado en base64
							$replaceVar = base64_decode($key); // Se decodifica el texto
							$replaceVar = str_replace($this->replace, $this->newText,$replaceVar); // Se reemplazan los valores
							$replaceVar = base64_encode($replaceVar); // Se codifica el nuevo texto
							$file_contents = str_replace($key,$replaceVar,$file_contents); // Se reemplaza en el archivo
					} 
					
				} 
				// Se buscan las cadenas de texto no codificadas
				$file_contents = str_replace($this->replace,$this->newText,$file_contents); // Se reemplza el texto en el archivo
				file_put_contents($path_to_file,$file_contents); // Se guarda el archivo
			} 
			if($this->NOF > 0){ // Si la cantida de archivos es mayor a 0
				$this->progress = ($count*100)/$this->NOF; // Se calcula el porcentaje del progreso
				$_SESSION['progress'] = $this->progress; // Se guarda el porcentaje en una sesion
			}
		} 
	}
	/**
	 * [countFiles Función que cuenta los archivos que seran procesados]
	 * @return [No retorna valores] 
	 */
	public function countFiles(){
		$di = new RecursiveDirectoryIterator($this->folderUrl);
		foreach (new RecursiveIteratorIterator($di) as $filename => $file) {
			$this->NOF++;
		}
	}
	/**
	 * [downloadNewZip Función que imprime la ruta al nuevo zip generado]
	 * @return [No retorna valores]
	 */
	public function downloadNewZip(){
		if (file_exists($this->folderUrl)) {
			$newFile = $this::createZip();
			file_put_contents('file.txt', print_r($newFile, true));
			$this::deleteAll($this->relativeFilePath);
			if($newFile !== ""){
			  echo $newFile;
			}
		    exit;
		}
	}
	/**
	 * [createZip Función que crea el archivo ZIP con las cadenas reemplazadas]
	 * @return [string] (Nombre del archivo)
	 */
	private function createZip(){
		$filename = explode('.',$this->filename);
		$filename = $filename[0];
		$this->relativeFilePath = $this->storeFolder.$filename;
		$zip = new ZipArchive(); 
		$zip->open($this->relativeFilePath.'_new.zip', ZipArchive::CREATE); 

		$dirName = $this->relativeFilePath; 

		if (!is_dir($dirName)) { 
		    throw new Exception('Directory ' . $dirName . ' does not exist'); 
		} 

		//$dirName = realpath($dirName); 
		if (substr($dirName, -1) != '/') { 
		    $dirName.= '/'; 
		} 
		//$dirname=end(explode('\\', $dirname));
		
		$dirStack = array($dirName);
		//Find the index where the last dir starts 
		$cutFrom = 0;//strrpos(substr($dirName, 0, 0), '/')+1; 
		
		while (!empty($dirStack)) { 
		    $currentDir = array_pop($dirStack); 
		    $filesToAdd = array(); 

		    $dir = dir($currentDir); 
		    while (false !== ($node = $dir->read())) { 
		        if (($node == '..') || ($node == '.')) { 
		            continue; 
		        } 
		        if (is_dir($currentDir . $node)) { 
		            array_push($dirStack, $currentDir . $node . '/'); 
		        } 
		        if (is_file($currentDir . $node)) { 
		            $filesToAdd[] = $node; 
		        } 
		    } 

		    $localDir = substr($currentDir, $cutFrom); 
		    $zip->addEmptyDir($localDir); 
		    foreach ($filesToAdd as $file) { 
		        $zip->addFile($currentDir . $file, $localDir . $file); 
		    } 
		} 

		$zip->close();
		return file_exists($this->relativeFilePath."_new.zip")?$this->relativeFilePath."_new.zip":"";
	}
	/**
	 * [deleteAll Función que elimina el directorio creado al momento de descomprimir el zip]
	 * @param  string  $directory Ruta a la carpeta
	 * @param  boolean $empty     Variable que indica si el directorio esta vacio
	 * @return boolean Confirma si fueron eliminados todos los directrios
	 */
    public function deleteAll($directory, $empty = false) { 
	    if(substr($directory,-1) == "/") { 
	        $directory = substr($directory,0,-1); 
	    } 

	    if(!file_exists($directory) || !is_dir($directory)) { 
	        return false; 
	    } elseif(!is_readable($directory)) { 
	        return false; 
	    } else { 
	        $directoryHandle = opendir($directory); 
	        
	        while ($contents = readdir($directoryHandle)) { 
	            if($contents != '.' && $contents != '..') { 
	                $path = $directory . "/" . $contents; 
	                
	                if(is_dir($path)) { 
	                    $this::deleteAll($path); 
	                } else { 
	                    unlink($path); 
	                } 
	            } 
	        } 
	        
	        closedir($directoryHandle); 

	        if($empty == false) { 
	            if(!rmdir($directory)) { 
	                return false; 
	            } 
	        } 
	        
	        return true; 
	    } 
	}  
}

?>