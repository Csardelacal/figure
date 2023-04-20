<?php namespace app\controllers;

use app\figure\pipeline\video\VideoContainStage;
use app\figure\pipeline\video\VideoCoverStage;
use app\figure\pipeline\video\VideoCropStage;
use app\figure\pipeline\video\VideoPipeline;
use app\figure\pipeline\video\VideoPipelineContext;
use app\figure\pipeline\video\VideoScaleDownStage;
use app\models\UploadModel;
use magic3w\http\url\reflection\QueryString;
use spitfire\exceptions\user\NotFoundException;
use spitfire\io\stream\Stream;
use spitfire\mvc\Controller;

class VideoController extends Controller
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
		
		$filename = $upload->getFile()->getFileName();
		$contents = storage()->readStream($filename);
		
		/**
		 * Since we currently have no internal documentation, it seems obvious to follow a well
		 * established documentation and try our best to be compatible with it. In this case,
		 * the excellent docs for Cloudflare images should do the trick.
		 *
		 * @see https://developers.cloudflare.com/images/image-resizing/url-format/#options
		 */
		$transforms = QueryString::parse($transform, ',');
		$pipeline   = new VideoPipeline();
		
		
		if ($transforms['fit'] === 'scale-down') {
			$pipeline->push(new VideoScaleDownStage($transforms['width']?? null, $transforms['height']?? null));
		}
		elseif ($transforms['fit'] === 'contain') {
			$pipeline->push(new VideoContainStage($transforms['width']?? null, $transforms['height']?? null));
		}
		elseif ($transforms['fit'] === 'crop') {
			$pipeline->push(new VideoCropStage($transforms['width']?? null, $transforms['height']?? null));
		}
		elseif ($transforms['fit'] === 'cover') {
			$pipeline->push(new VideoCoverStage($transforms['width']?? null, $transforms['height']?? null));
		}
		
		$tmpfilename = tempnam(sys_get_temp_dir(), 'ffmpeg') . '.mp4';
		$tmpfile = fopen($tmpfilename, 'w+');
		Stream::fromHandle($tmpfile)->write($contents->getContents());
		$result = $pipeline->apply(VideoPipelineContext::fromFile(stream_get_meta_data($tmpfile)['uri']));
		
		switch ($transforms['format']?? null) {
			case 'mp4':
			default:
				$contentType = 'video/mp4';
				break;
		}
		
		$result = Stream::fromString($result->render($contentType));
		fclose($tmpfile);
		unlink($tmpfilename);
		
		return response($result, 200, ['Content-type' => $contentType]);
	}
}
