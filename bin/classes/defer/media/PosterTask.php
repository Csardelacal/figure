<?php namespace defer\media;

use spitfire\core\async\Result;

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

class PosterTask extends \spitfire\core\async\Task
{
	
	private $extensions = [
		'image/png' => 'png',
		'image/jpeg' => 'jpg',
		'image/webp' => 'webp'
	];
	
	public function body(): \spitfire\core\async\Result
	{
		
		list($uid, $contentType) = $this->getSettings();
		
		/*
		 * Find the original file, so we can use it to generate compressed versions
		 * of the file we intended.
		 */
		$original = db()->table('media')->get('_id', $uid)->first(true);
		$upload   = $original->upload;
		
		$cover    = db()->table('media')->get('upload', $upload)->where('target', $original->target)->where('cover', true)->where('mime', $contentType)->first()?: db()->table('media')->newRecord();
		
		
		/*
		 * Set the basic properties of the media, so we already know it's good.
		 */
		$cover->target = $original->target;
		$cover->upload = $upload;
		$cover->cover  = true;
		$cover->width  = $original->width;
		$cover->height = $original->height;
		
		
		#Figure does not generate GIF files, instead it will produce MP4 files.
		$extension = $this->extensions[$contentType];
		
		if ($extension == pathinfo($original->file, PATHINFO_EXTENSION)) {
			$cover->file = $original->file;
			$cover->mime = $original->mime;
			$cover->length = $original->length;
			$cover->store();
			
			$this->after($cover->_id, $contentType);
			return new Result('Image could be used directly as poster, not recompressing');
		}
		
		
		/*
		 * Prepare a manipulator for the file, so we can properly generate compressed
		 * versions of the file.
		 */
		$file        = storage()->retrieve($original->file);
		$manipulator = media()->load($file);
		$posterManipulator = $manipulator->poster()->background(255, 255, 255);
		
		$outputPoster = storage()->retrieve(\spitfire\core\Environment::get('uploads.directory'), uniqid(null, true) . '.' . $extension);
		$posterManipulator->store($outputPoster);
		
		#Check if the cover is actually a different file. If it's not we can store the same reference.
		
		$cover->file = $outputPoster->uri();
		$cover->mime = $outputPoster->mime();
		list($cover->width, $cover->height) = $posterManipulator->dimensions();
		
		$cover->store();
		$this->after($cover->_id, $contentType);
		
		return new \spitfire\core\async\Result('Successfully generated a poster for the media file');
	}
	
	/**
	 * Allows a compressor to invoke follow-up tasks (like generating a poster of 
	 * the compressed file or scaling it to a smaller version)
	 */
	public function after($_id, $contentType)
	{
		//By default this is the last thing we do, but we can override behaviors here.
	}
}
