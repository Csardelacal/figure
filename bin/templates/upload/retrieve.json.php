<?php

/* 
 * The MIT License
 *
 * Copyright 2020 César de la Cal Bretschneider <cesar@magic3w.com>.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */


$media = db()->table('media')->get('upload', $upload)->where('cover', false)->all()->each(function ($e) use ($upload, $secret) {
	
	$posters = db()->table('media')->get('upload', $upload)->where('target', $e->target)->where('cover', true)->all()->each(function ($e) use ($secret) {
		return [
			'size'   => $e->target,
			'mime'   => $e->mime,
			'width'  => $e->width,
			'height' => $e->height,
			'url'    => $e->url($secret)
		];
	})->toArray();
	
	return [
		'size'   => $e->target,
		'mime'   => $e->mime,
		'width'  => $e->width,
		'height' => $e->height,
		'url'    => $e->url($secret),
		'poster' => $posters
	];
});

current_context()->response->getHeaders()->contentType('json');
current_context()->response->getHeaders()->set('Access-Control-Allow-Origin', '*');
current_context()->response->getHeaders()->set('Access-Control-Allow-Headers', 'Content-type');

echo json_encode([
	'payload' => [
		'id'     => $upload->_id,
		'type'   => $upload->type,
		'created'=> $upload->created,
		'secret' => ['expires' => $secret->expires],
		'media'  => array_combine($media->extract('size')->toArray(), $media->toArray())
	]
]);
