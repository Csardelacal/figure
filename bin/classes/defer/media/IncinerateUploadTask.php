<?php namespace defer\media;

use MediaModel;
use SecretModel;
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

class IncinerateUploadTask extends Task
{
	
	public function body(): Result
	{
		$id     = $this->getSettings();
		$upload = db()->table('upload')->get('_id', $id)->first(true);
		
		/*
		 * If an upload is not expired, it can obviously not be incinerated
		 */
		if ($upload->expires === null) {
			return new Result('Upload is marked as unexpired. It cannot be incinerated.');
		}
		
		/*
		 * In the event that the expiration date was pushed back, the incineration
		 * will be defered.
		 */
		if ($upload->expires > time()) {
			Async::defer($upload->expires, $this);
			return new Result('Upload is not yet expired. Incineration has been defered.');
		}
		
		/*
		 * If there's dangling media, we need to get rid of it before we remove the
		 * upload, otherwise the files will get orphaned.
		 */
		db()->table('media')->get('upload', $upload)->all()->each(function (MediaModel$e) {
			$e->delete();
		});
		
		/*
		 * Although secrets should be deleted automatically by the database due to 
		 * reference policy, we make sure to delete the secrets too.
		 */
		db()->table('secret')->get('upload', $upload)->all()->each(function (SecretModel$e) {
			$e->delete();
		});
		
		/*
		 * The secrets and media are removed, we can now incinerate the upload too.
		 */
		$upload->delete();
		return new Result('Upload incinerated.');
	}
}
