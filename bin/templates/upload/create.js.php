
<?php 

current_context()->response->getHeaders()->contentType('application/javascript');
current_context()->response->getHeaders()->set('Access-Control-Allow-Origin', '*');
current_context()->response->getHeaders()->set('Access-Control-Allow-Headers', 'Content-type');

?>//<script>
	
/**
 * This basic upload helper will allow applications to quickly and easily connect
 * to a figure server to create uploads.
 * 
 * All they need to do is add the upload/create.js script to their application and
 * a bit of glue logic so that previews / progress bars are displayed. Figure will
 * start processing the file in the background, so the image is completely processed
 * by the time the user finished uploading their file.
 * 
 * @returns {undefined}
 */
(function () {
	
	var url = '<?= url('upload', 'create')->setExtension('json')->absolute() ?>';
	const maxLength = <?= spitfire\io\Upload::getMaxUploadSize()->getSize() ?>;
	
	var toMB = function (bytes) {
		return (bytes / 1024 / 1024).toFixed(1);
	}
	
	var Upload = function (url) {
		this.url = url;
	};
	
	Upload.prototype = {
		upload: function (file, cb, op, err) {
			var xhr = new XMLHttpRequest();
			var payload = new FormData();
			
			xhr.onreadystatechange = function () {
				if (xhr.readyState !== 4 ) { return; }
				if (xhr.status !== 200) { throw 'Network error'; }
				
				cb(JSON.parse(xhr.responseText));
			};
			
			if (file.size > maxLength) {
				err && err('Your upload is ' + toMB(file.size) + 'MB. Upload limit is ' + toMB(maxLength) + 'MB. Cannot continue');
				throw 'Figure: Upload size was too large';
			}
			
			if (op) {
				xhr.upload.onprogress = function (e) { op('upload', e.loaded / e.total); }
				xhr.onprogress = function (e) { op('download', e.loaded / e.total); }
			}
			
			payload.append('media', file);
			xhr.open('POST', this.url);
			xhr.send(payload);
		}
	};
	
	try { depend('figure/upload', [], function () { return (new Upload(url)) }); }
	catch (e) { console.info('Figure upload could not initialize depend()'); }
	
	window.m3w = window.m3w || {};
	window.m3w.figure = window.m3w.figure || {};
	window.m3w.figure.upload = new Upload(url);
	
}());