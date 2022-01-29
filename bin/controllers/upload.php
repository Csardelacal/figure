<?php

use spitfire\exceptions\HTTPMethodException;
use spitfire\exceptions\PublicException;
use spitfire\io\Upload;

/* 
 * The MIT License
 *
 * Copyright 2020 cesar.
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

class UploadController extends BaseController
{
	
	/**
	 * 
	 * @throws HTTPMethodException
	 */
	public function create()
	{
		
		try {
			if (!$this->request->isPost()) {
				throw new HTTPMethodException('Not Posted'); 
			}
			if (!($_POST['media'] instanceof Upload)) {
				throw new PublicException('Invalid media', 400); 
			}
			
			$upload = db()->table('upload')->newRecord();
			$upload->app     = null;
			$upload->expires = time() + 86400 * 30;
			
			$manipulator = media()->load(storage()->retrieve('file://' . $_POST['media']->get('tmp_name')));
			
			if ($manipulator instanceof \spitfire\io\media\FFMPEGManipulator && $manipulator->hasAudio()) {
				$upload->type = UploadModel::TYPE_VIDEO; 
			}
			elseif ($manipulator instanceof \spitfire\io\media\FFMPEGManipulator && !$manipulator->hasAudio()) {
				$upload->type = UploadModel::TYPE_ANIMATION; 
			}
			else {
				$upload->type = UploadModel::TYPE_IMAGE; 
			}
			
			$upload->placeholder = $manipulator->poster()->fit(1, 1)->at(0, 0);
			$upload->store();
			
			$media = db()->table('media')->newRecord();
			$media->target = MediaModel::TARGET_ORIGINAL;
			$media->file   = $_POST['media']->store()->uri();
			$media->cover  = false;
			$media->upload = $upload;
			$media->mime   = $_POST['media']->store()->mime();
			$media->length = $_POST['media']->store()->length();
			$media->store();
			
			
			$secret = db()->table('secret')->newRecord();
			$secret->upload = $upload;
			$secret->store();
			
			/*
			 * Process the upload. The server will proceed to generate a variety of
			 * different formats of the upload to ensure that the client can fetch a version
			 * that will work for them.
			 */
			\spitfire\core\async\Async::defer(0, new \defer\media\ProcessMediaTask($media->_id));
			\spitfire\core\async\Async::defer($upload->expires, new \defer\media\IncinerateUploadTask($upload->_id));
			
			#Redirects only work for the HTML variants
			$this->view->set('upload', $upload);
			$this->view->set('secret', $secret);
		} 
		catch (HTTPMethodException$ex) {
			/*Do nothing, the form will be presented here*/
		}
	}
	
	public function claim(UploadModel$model, $secret)
	{
		
		if (!$this->authapp) {
			throw new PublicException('Unauthorized application', 403);
		}
		
		/*
		 * The client claiming the image, can expect the image to be an image, an 
		 * animation, a video or any of several.
		 * 
		 * This is extremely useful when you have an application that prohibits animated
		 * avatars, since it allows the application to not claim the image if it contains
		 * video or animation.
		 */
		$accepted = (array)($_GET['accept']?? null);
		if ($accepted && !in_array($model->type, $accepted)) {
			throw new PublicException('The image failed to meet expected type', 400);
		}
		
		#Check the secret is available
		db()->table('secret')->get('upload', $model)->where('secret', $secret)->first(true);
		
		
		$model->app = $this->authapp->getId();
		$model->expires = null;
		$model->store();
		
		$this->view->set('upload', $model);
	}
	
	public function retrieve(UploadModel$model, $secret)
	{
		$this->response->getHeaders()->set('Cache-control', 'public, max-age=604800, immutable');
		$this->response->getHeaders()->set('Expires', date('r', time() + 86400 * 90));
		
		#Check the secret is available
		$secret = db()->table('secret')->get('upload', $model)->where('secret', $secret)->first(true);
		$this->view->set('upload', $model);
		$this->view->set('secret', $secret);
	}
	
	public function delete(UploadModel$model)
	{
		
		if (!$this->authapp) {
			throw new PublicException('Application could not be authorized', 403);
		}
		
		$model->expires = time();
		$model->store();
		
		\spitfire\core\async\Async::defer($upload->expires, new \defer\media\IncinerateUploadTask($upload->_id));
	}
}
