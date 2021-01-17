<?php namespace defer\media;

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

abstract class Compressor extends \spitfire\core\async\Task
{
	
	const SETTING_RATIO_MAX  = 'ratioMax';
	const SETTING_RATIO_MIN  = 'ratioMin';
	const SETTING_HEIGHT_MAX = 'heightMax';
	const SETTING_WIDTH_MAX  = 'widthMax';
	const SETTING_RM_ALPHA   = 'remove-alpha';
	
	
	const SETTING_FORCE_TYPE = 'forceContentType';
	
	public function body(): \spitfire\core\async\Result {
		
		/*
		 * Find the original file, so we can use it to generate compressed versions
		 * of the file we intended.
		 */
		$original = db()->table('media')->get('_id', $this->getSettings())->first(true);
		$upload   = $original->upload;
		$target   = db()->table('media')->get('upload', $upload)->where('target', $this->getTarget())->where('cover', $original->cover)->where('mime', $original->mime)->first()?: db()->table('media')->newRecord();
		
		/*
		 * Prepare a manipulator for the file, so we can properly generate compressed
		 * versions of the file.
		 */
		$file        = storage()->retrieve($original->file);
		$manipulator = media()->load($file);
		
		/*
		 * Set the basic properties of the media, so we already know it's good.
		 */
		$target->target = $this->getTarget();
		$target->upload = $upload;
		$target->cover  = $original->cover;
		
		/*
		 * We now perform a series fo checks, determining whether the file we already 
		 * have, is in the spec of the files we wish to generate. If that's the case,
		 * we can stop here.
		 */
		$valid = true;
		$parameters = $this->getOptions();
		
		list($width, $height) = $manipulator->dimensions();
		
		#Check if the sizes are appropriate
		if ($parameters[self::SETTING_HEIGHT_MAX] && $height > $parameters[self::SETTING_HEIGHT_MAX]) { $valid = false; }
		if ($parameters[self::SETTING_WIDTH_MAX] && $width  > $parameters[self::SETTING_WIDTH_MAX])  { $valid = false; }
		
		if ($parameters[self::SETTING_RM_ALPHA])  { $valid = false; }
		if ($file->mime() === 'image/gif')  { $valid = false; } #GIFs are accepted as an input format, but cannot be properly processed
		
		#Check if the ratios are in spec
		if ($parameters[self::SETTING_RATIO_MAX] && $width / $height  > $parameters[self::SETTING_RATIO_MAX])  { $valid = false; }
		if ($parameters[self::SETTING_RATIO_MIN] && $width / $height  < $parameters[self::SETTING_RATIO_MIN])  { $valid = false; }
		
		if ($valid) {
			$target->file = $file->uri();
			$target->height = $height;
			$target->width  = $width;
			$target->length = $original->length;
			$target->mime   = $file->mime();
			$target->store();
			
			$this->after($target->_id);
			
			return new \spitfire\core\async\Result('Success! Compression was not required');
		}
		
		#First, check if the expected ratios are going to work. 
		#If that's not the case, the system will chop the width or height to make it fit.
		if ($parameters[self::SETTING_RATIO_MAX] && $width / $height  > $parameters[self::SETTING_RATIO_MAX]) {
			$width = $height * $parameters[self::SETTING_RATIO_MAX];
		}
		
		if ($parameters[self::SETTING_RATIO_MIN] && $width / $height  < $parameters[self::SETTING_RATIO_MIN]) {
			$height = $width / $parameters[self::SETTING_RATIO_MIN];
		}
		
		#After that, check if the height needs to be addressed
		if ($parameters[self::SETTING_HEIGHT_MAX] && $height > $parameters[self::SETTING_HEIGHT_MAX]) {
			$reduction = $parameters[self::SETTING_HEIGHT_MAX] / $height;
			$height*= $reduction;
			$width *= $reduction;
		}
		
		if ($parameters[self::SETTING_WIDTH_MAX] && $width > $parameters[self::SETTING_WIDTH_MAX]) {
			$reduction = $parameters[self::SETTING_WIDTH_MAX] / $width;
			$height*= $reduction;
			$width *= $reduction;
		}
		
		#If the image is to be generated without alpha channel, we do that
		if ($parameters[self::SETTING_RM_ALPHA]) {
			$manipulator->background(255, 255, 255);
		}
		
		#Crop and scale the image accordingly
		$manipulator->fit((int)$width, (int)$height);
		
		#Generate the output targets
		$target->height = (int)$height;
		$target->width  = (int)$width;
		
		#Figure does not generate GIF files, instead it will produce MP4 files.
		$extension = pathinfo($file->uri(), PATHINFO_EXTENSION);
		if ($extension == 'gif') {
			$extension = 'mp4';
		}
		
		$output = storage()->retrieve(\spitfire\core\Environment::get('uploads.directory'), uniqid(null, true) . '.' . $extension);
		
		$manipulator->store($output);
		
		$target->file = $output->uri();
		$target->mime = $output->mime();
		$target->length = $output->length();
		
		$target->store();
		$this->after($target->_id);
		
		return new \spitfire\core\async\Result('Successfully compressed the media file');
	}
	
	public function getOptions() {
		return [
			self::SETTING_HEIGHT_MAX => null,
			self::SETTING_WIDTH_MAX  => null,
			self::SETTING_RATIO_MAX  => null,
			self::SETTING_RATIO_MIN  => null,
			self::SETTING_FORCE_TYPE => null,
			self::SETTING_RM_ALPHA   => false
		];
	}
	
	abstract public function getTarget();
	
	/**
	 * Allows a compressor to invoke follow-up tasks (like generating a poster of 
	 * the compressed file or scaling it to a smaller version)
	 */
	abstract public function after($_id);
	

}
