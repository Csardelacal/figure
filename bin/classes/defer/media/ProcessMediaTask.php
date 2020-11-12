<?php namespace defer\media;

use defer\media\thumb\ThumbCompressorCappedLarge;
use defer\media\thumb\ThumbCompressorSquare;
use spitfire\core\async\Async;
use spitfire\core\async\Result;
use spitfire\core\async\Task;
use function db;

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

class ProcessMediaTask extends Task
{
	
	
	public function body(): Result {
		
		/*
		 * Load the original upload and start creating tasks for it.
		 */
		$original = db()->table('media')->get('_id', $this->getSettings())->first(true);
		$man      = media()->load(storage()->retrieve($original->file));

		list($width, $height) = $man->dimensions();
		
		$original->width = $width;
		$original->height = $height;
		$original->store();
		
		/*
		 * GIFs are a terrible medium to manipulate, so instead we're going for 
		 * replacing it with an MPEG file and deleting the old media
		 */
		if ($original->mime === 'image/gif') {
			$old = storage()->retrieve($original->file);
			$new = storage()->retrieve($original->file . '.mp4');
			
			/*
			 * If the upload was a gif, we convert the file to a mp4 file by reencoding
			 * it scaled to the same size it already was.
			 */
			$man->scale($width)->store($new);
			
			$original->mime = $new->mime();
			$original->file = $new->uri();
			$original->store();
			
			$old->delete();
			
			foreach (db()->table('secret')->get('upload', $original->upload)->all() as $secret) {
				\spitfire\cache\MemcachedAdapter::getInstance()->delete('url_media_' . $original->_id . '_' . $secret->_id);
			}
		}
		
		Async::defer(0, new ThumbCompressorSquare($original->_id));
		Async::defer(0, new ThumbCompressorCappedLarge($original->_id));
		Async::defer(0, new scaled\ScaledCompressorXLarge($original->_id));
		return new Result('Queued tasks to process media');
	}

}
