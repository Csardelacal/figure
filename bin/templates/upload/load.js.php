<?php 

current_context()->response->getHeaders()->contentType('application/javascript');
current_context()->response->getHeaders()->set('Access-Control-Allow-Origin', '*');
current_context()->response->getHeaders()->set('Access-Control-Allow-Headers', 'Content-type');

?>//<script>

(function () {
	var url = '<?= url()->absolute() ?>';
	var loaded = {};
	
	var canUseWebP = function () {
		var elem = document.createElement('canvas');

		if (!!(elem.getContext && elem.getContext('2d'))) {
			 // was able or not to get WebP representation
			 return elem.toDataURL('image/webp').indexOf('data:image/webp') == 0;
		}

		// very old browser like IE 8, canvas not supported
		return false;
  }();
	
	var getCandidate = function (haystack, needle) {
		
		if (!haystack[needle.size]) { 
			console.log('Failed looking for size ' + needle.size ); 
			return false; 
		}
		
		if (needle.poster && haystack[needle.size].poster.length === 0) { 
			return false; 
		}
		
		if (needle.poster && needle.format) { 
			for (var j = 0; j < haystack[needle.size].poster.length; j++) {
				if (haystack[needle.size].poster[j].mime === needle.format) {
					return haystack[needle.size].poster[j];
				}
			}

			console.error('No image found');
			return false;
		}
		
		if (needle.poster && canUseWebP) { 
			for (var j = 0; j < haystack[needle.size].poster.length; j++) {
				if (haystack[needle.size].poster[j].mime === 'image/webp') {
					console.error('Browser supports webp, used it');
					return haystack[needle.size].poster[j];
				}
			}

			console.error('Would have used WEBP, did not find candidate');
		}
		
		return needle.poster? haystack[needle.size].poster[0] : haystack[needle.size];
	};
	
	var fetchSrc = function (id, secret) {
		if (loaded[id]) {
			return loaded[id];
		};
		
		var promise = new Promise(function (success) {
		
			var xhr = new XMLHttpRequest();
			xhr.onreadystatechange = function () {
				if (xhr.readyState != 4 || xhr.status != 200) { return; }
				var payload = JSON.parse(xhr.responseText).payload;
				success(payload);
			};

			xhr.open('GET', url + 'upload/retrieve/' + id + '/' + secret + '.json');
			xhr.send();
		});
		
		loaded[id] = promise;
		return promise;
	};
	
	var load = function (figure) {
		var img = document.createElement('img');
		
		var meta = figure.dataset.src.split(':');
		var _sizes = figure.dataset.size.split(',');
		var sizes = [];
		
		_sizes.forEach(function (e) {
			var s = e.trim();
			var pieces = s.split(':');
			
			sizes.push({
				size : pieces[0] || 'original',
				poster : pieces[1] == 'poster' || false,
				format : pieces[2] || null
			});
		});
		
		fetchSrc(meta[0], meta[1]).then(function (payload) {
			
			var candidate = {
				mime: 'image/jpeg',
				url: 'https://external-content.duckduckgo.com/iu/?u=http%3A%2F%2Fcomtech2.com%2Fwp-content%2Fuploads%2F2016%2F09%2Fdrib-processing.png&f=1&nofb=1' 
			};
			
		for (var i = sizes.length; i > 0; i--) {
				var candidate = getCandidate(payload.media, sizes[i - 1]) || candidate;
			}
			
			if (candidate.mime.substring(0, 5) == 'video') {
				img = document.createElement('video');
				
				if (payload.type === 'animation') {
					img.loop = 'loop';
					img.autoplay = 'autoplay';
				}
				
				if (payload.type === 'video') {
					img.removeAttribute('loop');
					img.removeAttribute('autoplay');
					img.controls = true;
				}
			} 
			
			img.src = candidate.url;
			figure.parentNode.insertBefore(img, figure);
			figure.parentNode.removeChild(figure);
			
			img.id = figure.id;
			img.className = figure.className;
			img.style.cssText = figure.style.cssText;
		})
		
	}
	
	var loadall = function () {
		var figures = document.querySelectorAll('figure[data-src]');

		for (var i = 0; i < figures.length; i++) {
			load(figures[i]);
		}
	};
	
	window.m3w = window.m3w || {};
	window.m3w.figure = window.m3w.figure || {};
	window.m3w.figure.load = loadall;
	depend && depend('figure/load', [], function () { return loadall; });
	
}());