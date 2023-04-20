<?php namespace app\controllers;

use app\classes\utils\FeatureDetect;
use app\figure\pipeline\image\ImageContainStage;
use app\figure\pipeline\image\ImageCoverStage;
use app\figure\pipeline\image\ImageCropStage;
use app\figure\pipeline\image\ImageGravityAutoStage;
use app\figure\pipeline\image\ImageGravitySideStage;
use app\figure\pipeline\image\ImagePipeline;
use app\figure\pipeline\image\ImagePipelineContext;
use app\figure\pipeline\image\ImageScaleDownStage;
use app\models\UploadModel;
use magic3w\http\url\reflection\QueryString;
use spitfire\exceptions\user\NotFoundException;
use spitfire\io\stream\Stream;
use spitfire\mvc\Controller;

class ImageController extends Controller
{
	
	public function retrieve($id, $expiration, $transform, $salt, $hash)
	{
		/**
		 * This secret is managed by figure, the "key" is never revealed to any client. This
		 * allows for an efficient mechanism of generating URLs that the caching servers can
		 * interpret and cache.
		 *
		 * This secret should be shared across the PHP and Nginx caching servers to ensure that
		 * the caching servers can parse the request and perform caching appropriately.
		 *
		 * It implies that clients need to fetch a signed url from figure. Unless they are provided
		 * the secret to reduce the amount of round-trips required. Sharing the secret is generally
		 * discouraged and should only be done if the servers truly exchange heaps of data.
		 */
		$secret = config('figure.publish.secret');
		
		$payload = implode('.', [
			$id,
			$expiration,
			$transform,
			$salt,
			$secret
		]);
		
		$expected = sha1($payload);
		echo $expected, PHP_EOL;
		
		assume($expiration > time(), 'Link is expired');
		assume($hash === $expected, 'Invalid signature');
		
		
		/**
		 * @var UploadModel
		 */
		$upload = db()->fetch(UploadModel::class, $id);
		
		if (!$upload) {
			throw new NotFoundException('No upload with the given id');
		}
		
		$filename = $upload->getFile()->getPoster();
		$contents = storage()->readStream($filename);
		
		/**
		 * Since we currently have no internal documentation, it seems obvious to follow a well
		 * established documentation and try our best to be compatible with it. In this case,
		 * the excellent docs for Cloudflare images should do the trick.
		 *
		 * @see https://developers.cloudflare.com/images/image-resizing/url-format/#options
		 */
		$transforms = QueryString::parse($transform, ',');
		$pipeline   = new ImagePipeline();
		
		/**
		 * Determine the client requested gravity calculation. By default the system will assume using
		 * the saliency based calculation.
		 */
		$gravity = $transforms['gravity']?? 'auto';
		
		switch ($gravity) {
			/**
			 * This is the default, once manual gravity is enabled this will need some if-else
			 * around it.
			 *
			 * @todo This needs to be cached. Saliency calculations are taxing on the server,
			 * especially in PHP.
			 */
			case 'auto':
				$pipeline->push(new ImageGravityAutoStage);
				break;
			case 'left':
				$pipeline->push(ImageGravitySideStage::left());
				break;
			case 'right':
				$pipeline->push(ImageGravitySideStage::right());
				break;
			case 'top':
				$pipeline->push(ImageGravitySideStage::top());
				break;
			case 'bottom':
				$pipeline->push(ImageGravitySideStage::bottom());
				break;
			/**
			 * If the client defined a custom gravity calculation, the application should respect that,
			 * the application consuming the endpoint will have to pass a float value for x and y separated
			 * by a lowercase x.
			 */
			default:
				$pipeline->push(ImageGravitySideStage::fromString($gravity));
				break;
		}
		
		if ($transforms['fit'] === 'scale-down') {
			$pipeline->push(new ImageScaleDownStage($transforms['width']?? null, $transforms['height']?? null));
		}
		elseif ($transforms['fit'] === 'contain') {
			$pipeline->push(new ImageContainStage($transforms['width']?? null, $transforms['height']?? null));
		}
		elseif ($transforms['fit'] === 'crop') {
			$pipeline->push(new ImageCropStage($transforms['width']?? null, $transforms['height']?? null));
		}
		elseif ($transforms['fit'] === 'cover') {
			$pipeline->push(new ImageCoverStage($transforms['width']?? null, $transforms['height']?? null));
		}
		
		switch ($upload->getFile()->getContentType()) {
			case 'image/jpg':
			case 'image/jpeg':
			case 'image/gif':
			case 'video/mp4':
				$filetype = 'image/jpeg';
				break;
			case 'image/png':
				$filetype = 'image/png';
				break;
			case 'image/webp':
				$contentType = 'image/webp';
				break;
		}
		
		$result = $pipeline->apply(ImagePipelineContext::fromStream($filetype, $contents));
		
		switch ($transforms['format']?? null) {
			case 'jpg':
			case 'jpeg':
				$contentType = 'image/jpeg';
				break;
			case 'png':
				$contentType = 'image/png';
				break;
			case 'webp':
				$contentType = 'image/webp';
				break;
			default:
				$contentType = FeatureDetect::webp()? 'image/webp' : 'image/jpeg';
				break;
		}
		
		return response(Stream::fromString($result->render($contentType)), 200, ['Content-type' => $contentType]);
		// $stream = Stream::fromString($image->writeToBuffer('.jpeg'));
		// return response($stream, 200, ['Content-type' => '.jpeg']);
	}
}
