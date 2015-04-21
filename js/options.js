$(document).on('ready',function(){
		$('#saveValues').click(function(){
			$.ajax({
				url:'registerValues.php',
				type:'post',
				data:{replace:$('#replace').val(),to:$('#to').val()},
				success:function(data){
					if(data == 'true'){
						$('.dropzone').fadeIn(1000);
					}
				}
			})
		})
		$('.scan-folders').click(function(){
			$('.progress').show();
			$('.progress-bar').text('Contando Archivos');
			$.ajax({
				url:'scanFolders.php',
				success:function(response){
					console.log(response);
					$('.progress-bar').text('reemplazando');
					var a = setInterval(function(){
						$.ajax({
							url:'scanFolders.php',
							type:'post',
							data:{inf:true},
							success:function(data){
								$('.progress-bar').css({'width':data+'%'});
								console.log(data);
								if(data >= 100){
									clearInterval(a);
									$('.progress').hide();
								}
							}
						})
					},500)
				}
			})
		})
	})
	<?php
		if(isset($_SESSION['to']) && isset($_SESSION['replace'])){
	?>
	$('.dropzone').fadeIn(1000);
	Dropzone.options.myDropzone = {

		init: function() {
			var self = this;

			self.options.addRemoveLinks = true;
			self.options.dictRemoveFile = "Delete";

			self.on("addedfile", function(file) {
				console.log('new file added ', file);
				$('.progress-bar').css({'width':'0%'});
			});

			self.on("sending", function(file) {
			console.log('upload started', file);
				$('.progress').show();
			});
			self.on("uploadprogress", function(progress,percent,bytes) {
				$('.progress-bar').css({'width':percent+'%'});
				if(percent == 100){
					$('.progress-bar').text('Comprimiendo Nuevo Archivo');
				}
				console.log("progress ", percent);
				
			});
			self.on("success",function(data,response){
					console.log(response);
					$('.progress').delay(999).slideUp(999);
					window.open(document.URL+"/"+response,'_blank');
					$('.progress-bar').text('Cargando Archivo');				
			});
		}
	};
	<?php } ?>