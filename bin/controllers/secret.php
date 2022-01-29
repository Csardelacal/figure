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

class SecretController extends BaseController
{
	
	public function create(UploadModel$upload, $ttl = null)
	{
		
		if (!$this->authapp) {
			throw new PublicException('Only applications can create secrets', 403);
		}
		
		if ($upload->app !== $this->authapp->getId()) {
			throw new PublicException('This application is not the owner', 403);
		}
		
		$secret = db()->table('secret')->newRecord();
		$secret->upload = $upload;
		$secret->expires = $ttl? time() + $ttl : null;
		$secret->store();
		
		$this->view->set('secret', $secret);
	}
	
	public function expire(UploadModel$upload, $secret)
	{
		
		if (!$this->authapp) {
			throw new PublicException('Only applications can create secrets', 403);
		}
		
		if ($upload->app !== $this->authapp->getId()) {
			throw new PublicException('This application is not the owner', 403);
		}
		
		$model = db()->table('secret')->get('upload', $upload)->where('secret', $secret)->first();
		$model->expires = time();
		$model->store();
		
		$this->view->set('secret', $model);
	}
}
