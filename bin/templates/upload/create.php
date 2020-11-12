<?php

if (isset($upload)) { 
	current_context()->response->setBody('Redirect...')->getHeaders()->redirect(url('upload', 'retrieve', $upload->_id, $secret->secret)); 
	return;
}

?><div class="spacer medium"></div>

<noscript>
<div class="row l1">
	<div class="span l1">
		<div class="material">
			<form action="" method="POST" enctype="multipart/form-data">
				<input type="file" name="media">
				<input type="submit">
			</form>
		</div>
	</div>
</div>
</noscript>

<div style="display: none" id="upload-error">
	<div class="message error"></div>
</div>
<img style="display: none" id="figure-upload-preview">
<div id="figure-upload-result"></div>
<input type="file" style="display: none;" id="figure-upload-input">
<a id="figure-upload-link">Click to select a file</a>
<script src="<?= url('upload', 'create')->setExtension('js') ?>"></script>
<script>
(function() {
	var link = document.getElementById('figure-upload-link');
	var inpt = document.getElementById('figure-upload-input');
	var prvw = document.getElementById('figure-upload-preview');
	var rslt = document.getElementById('figure-upload-result');
	
	link.addEventListener('click', function () {
		inpt.click();
	});
	
	inpt.addEventListener('change', function () {
		var files = this.files;
		document.getElementById('upload-error').style.display = 'none';
		
		for (var i = 0; i < files.length; i++) {
			
			//Generate a preview
			var reader = new FileReader();
			reader.onload = function (e) {
				prvw.src = e.target.result;
				prvw.style.display = 'inline-block';
			};
			
			reader.readAsDataURL(files[i]);
			
			window.m3w.figure.upload.upload(files[i], function (response) {
				rslt.innerHTML = JSON.stringify(response);
				
				window.location = '<?= url() ?>upload/retrieve/' + response.payload.id + '/' + response.payload.secret;
			}, function (direction, progress) {
				rslt.innerHTML = JSON.stringify([direction, progress]);
			}, function (msg) {
				document.getElementById('upload-error').style.display = 'block';
				document.querySelector('#upload-error .message').textContent = msg;
			});
		}
	});
}());
</script>