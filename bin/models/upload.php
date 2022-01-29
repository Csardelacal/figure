<?php

use spitfire\Model;
use spitfire\storage\database\Schema;

/* 
 * The MIT License
 *
 * Copyright 2020  César de la Cal Bretschneider.
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

/**
 * An upload represents a file that a user deposited on Figure to be used at a 
 * later point in time by a software inside the network.
 * 
 * For example, when a user creates a product in an online store, Figure should 
 * make it easy to upload images to it that the server can later claim. Allowing
 * it to use Figure and it's resources to process and distribute the image.
 * 
 * Our use case is to have Figure to upload and distribute the files it receives
 * to a CloudyNAS cluster that will, in turn, take care of the distribution of 
 * the files themselves.
 * 
 * Funnily, the upload itself contains no file, it creates a media with the type 
 * "original" that processes the media accordingly.
 * 
 * @author César de la Cal Bretschneider<cesar@magic3w.com>
 */
class UploadModel extends Model
{
	
	const TYPE_IMAGE     = 'image';
	const TYPE_ANIMATION = 'animation';
	const TYPE_VIDEO     = 'video';
	
	/**
	 * 
	 * @param Schema $schema
	 * @return Schema
	 */
	public function definitions(Schema $schema)
	{
		$schema->type    = new EnumField(self::TYPE_IMAGE, self::TYPE_ANIMATION, self::TYPE_VIDEO, null);
		$schema->app     = new IntegerField(true);
		$schema->placeholder = new IntegerField(true); #Display a color while the image is loading
		$schema->created = new IntegerField(true);
		$schema->expires = new IntegerField(true);
	}
}
