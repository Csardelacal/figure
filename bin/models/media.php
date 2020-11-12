<?php

use spitfire\Model;
use spitfire\storage\database\Schema;

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
 
 

class MediaModel extends Model
{
	
	const TARGET_THUMB_SQUARE_SMALL  = 'square-s';
	const TARGET_THUMB_SQUARE_MEDIUM = 'square-m';
	
	const TARGET_THUMB_CAPPED_SMALL  = 'capped-s';
	const TARGET_THUMB_CAPPED_MEDIUM = 'capped-m';
	const TARGET_THUMB_CAPPED_LARGE  = 'capped-l';
	
	const TARGET_SCALED_SMALL        = 'scaled-s';
	const TARGET_SCALED_MEDIUM       = 'scaled-m';
	const TARGET_SCALED_LARGE        = 'scaled-l';
	const TARGET_SCALED_XLARGE       = 'scaled-xl';
	
	const TARGET_ORIGINAL            = 'original';
	
	/**
	 * 
	 * @param Schema $schema
	 * @return Schema
	 */
	public function definitions(Schema $schema) {
		
		$schema->upload  = new Reference('upload');
		$schema->file    = new StringField(1024);
		
		$schema->cover   = new BooleanField();
		$schema->target  = new EnumField(
			self::TARGET_ORIGINAL,
			self::TARGET_THUMB_SQUARE_SMALL,
			self::TARGET_THUMB_SQUARE_MEDIUM,
			self::TARGET_THUMB_CAPPED_SMALL,
			self::TARGET_THUMB_CAPPED_MEDIUM,
			self::TARGET_THUMB_CAPPED_LARGE,
			self::TARGET_SCALED_SMALL,
			self::TARGET_SCALED_MEDIUM,
			self::TARGET_SCALED_LARGE,
			self::TARGET_SCALED_XLARGE,
		);
		
		$schema->mime    = new StringField(100);
		$schema->width   = new IntegerField(true);
		$schema->height  = new IntegerField(true);
		$schema->length  = new IntegerField(true);
		
		$schema->created = new IntegerField(true);
	}
	
	public function onbeforesave() {
		if (!$this->created) {
			$this->created = time();
		}
	}
	
	public function url($secret) {
		
		$memcached = spitfire\cache\MemcachedAdapter::getInstance();
		
		return $memcached->get('url_media_' . $this->_id . '_' . $secret->_id, function () use ($secret) {
			$file = storage()->retrieve($this->file);

			try { return $file->publicURL($secret->expires - time()); }
			catch (BadMethodCallException$e) { return (string)url('media', 'download', $this->upload->_id, $secret->secret, $e->target)->absolute(); }
		});
	}
	
	public function delete() {
		/*
		 * Delete the data from the HDD before removing the database directory 
		 * entry.
		 */
		try {
			$handle = storage()->retrieve($this->file);
			$handle->delete();
		} 
		catch (\Exception$ex) {
			//TODO: Introduce proper logging to understand why this could not delete
			console()->error('Unable to delete ' . $this->file)->ln();
		}
		
		parent::delete();
	}

}
