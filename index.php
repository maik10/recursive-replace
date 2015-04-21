<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<script type="text/javascript" src="js/jquery.min.js"></script>
	<script type="text/javascript" src="js/dropzone.js"></script>
	<link rel="stylesheet" type="text/css" href="css/style.css">
	<link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap.min.css">
	<title>Carga de archivos</title>
</head>
<body>
	<div class="container">
		<div class="progress">
		  <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100">
		  Cargando Archivo
		  </div>
		</div>
			<form>
			<fieldset>
				<legend>Reemplazar Valores</legend>
				<label for="">Reemplazar:<input type="text" class="dataReplace form-control" id="replace" value="<?=isset($_SESSION['replace'])?$_SESSION['replace']:'' ?>"></label>
				<label for="">Por:<input type="text" class="dataReplace form-control" id="to" value="<?=isset($_SESSION['to'])?$_SESSION['to']:'' ?>"></label>
				<input class="btn btn-default" type="button" id="saveValues" value="Registrar Valores">
			</fieldset>
			</form>
			<button class="btn scan-folders btn-default">Recorrer Directorio de carpetas</button>
			<form action="upload.php" class="dropzone dz-clickable" id="myDropzone"></form>
	</div>
</body>
<script type="text/javascript">
$(document).on('ready',function(){$('#saveValues').click(function(){$.ajax({url:'registerValues.php', type:'post', data:{replace:$('#replace').val(),to:$('#to').val()}, success:function(data){if(data == 'true'){$('.dropzone').fadeIn(1000); } } }) }); $('.scan-folders').click(function(){$('.progress').show(); $('.progress-bar').text('Contando Archivos'); $.ajax({url:'scanFolders.php', success:function(response){console.log(response); $('.progress-bar').text('reemplazando'); var a = setInterval(function(){$.ajax({url:'scanFolders.php', type:'post', data:{inf:true}, success:function(data){$('.progress-bar').css({'width':data+'%'}); console.log(data); if(data >= 100){clearInterval(a); $('.progress').hide(); } } }); },500); } }); }); }); <?php if(isset($_SESSION['to']) && isset($_SESSION['replace'])){?> $('.dropzone').fadeIn(1000); Dropzone.options.myDropzone = {init: function() {var self = this; self.options.addRemoveLinks = true; self.options.dictRemoveFile = "Delete"; self.on("addedfile", function(file) {console.log('new file added ', file); $('.progress-bar').css({'width':'0%'}); }); self.on("sending", function(file) {console.log('upload started', file); $('.progress').show(); }); self.on("uploadprogress", function(progress,percent,bytes) {$('.progress-bar').css({'width':percent+'%'}); if(percent == 100){$('.progress-bar').text('Comprimiendo Nuevo Archivo'); } console.log("progress ", percent); }); self.on("success",function(data,response){console.log(response); $('.progress').delay(999).slideUp(999); window.open(document.URL+"/"+response,'_blank'); $('.progress-bar').text('Cargando Archivo'); }); } }; <?php } ?> </script>
</html>

