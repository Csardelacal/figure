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

class MediaController extends Controller
{
	
	private $sizes = [
		MediaModel::TARGET_ORIGINAL,
		MediaModel::TARGET_THUMB_SQUARE_SMALL,
		MediaModel::TARGET_THUMB_SQUARE_MEDIUM,
		MediaModel::TARGET_THUMB_CAPPED_SMALL,
		MediaModel::TARGET_THUMB_CAPPED_MEDIUM,
		MediaModel::TARGET_THUMB_CAPPED_LARGE,
		MediaModel::TARGET_SCALED_SMALL,
		MediaModel::TARGET_SCALED_MEDIUM,
		MediaModel::TARGET_SCALED_LARGE,
		MediaModel::TARGET_SCALED_XLARGE,
	];
	
	public function download(UploadModel$model, $secret, $size = MediaModel::TARGET_ORIGINAL)
	{
		
		if (!in_array($size, $this->sizes)) {
			throw new PrivateException('Invalid size parameter', 400);
		}
		
		$secret = db()->table('secret')->get('upload', $model)->where('secret', $secret)->first(true);
		
		if ($secret->expires !== null && $secret->expires < time()) {
			throw new PublicException('Link expired', 403);
		}
		
		$media = db()->table('media')->get('upload', $model)->where('target', $size)->where('cover', false)->first(true);
		$file = storage()->retrieve($media->file);
		
		if (!$file->exists()) {
			throw new PublicException('File does not exist in specified size', 404);
		}
		
		/**
		 * @todo Introduce spitfire external blob interface so applications can return a blob that can be directly linked to
		 */
		
		$url = $file->publicURL($secret->expires? $secret->expires - time() : null);
		
		if ($url) {
			$this->response->setBody('Redirect')->getHeaders()->redirect($url);
			return;
		}
		
		$this->response->getHeaders()->set('Content-type', $file->mime());
		$this->response->setBody($file->read());
	}
}
