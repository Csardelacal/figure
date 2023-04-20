<?php namespace app\controllers;

use app\classes\utils\Animated;
use app\classes\utils\LQIP;
use app\models\FileModel;
use app\models\UploadModel;
use FFMpeg\Coordinate\TimeCode;
use magic3w\http\url\reflection\QueryString;
use Psr\Http\Message\ServerRequestInterface;
use spitfire\io\stream\Stream;
use spitfire\mvc\Controller;

class UploadController extends Controller
{
	
	
	public function upload()
	{
		var_dump(spitfire()->provider()->get(ServerRequestInterface::class)->getUploadedFiles());
		/**
		 * @var UploadedFileInterface
		 */
		$file = spitfire()->provider()->get(ServerRequestInterface::class)->getUploadedFiles()['upload'];
		
		#Make a copy of the file to the hard drive
		$filename = sprintf('%s://%s_%s', config('storage.writeto'), uniqid('', true), $file->getClientFilename());
		
		
		/**
		 * The MD5 of the file is relevant for us, since it allows us to quickly scan the database for
		 * duplicates. This is by no means an exhaustive mechanism, but it makes it easy for us to detect
		 * reuploads within the system, which often occur just due to the fact that apps share their
		 * files with another.
		 */
		$tmp = $file->getStream()->getMetadata()['uri'];
		$md5 = md5_file($tmp);
		$mime = mime($tmp);
		
		/**
		 * Check if another file with the same md5 and the same length exists
		 *
		 * @var FileModel
		 */
		$_file = db()->from(FileModel::class)->where('md5', $md5)->first();
		
		$stream = $file->getStream();
		
		/**
		 * If the file does not exist, we create it. Please note that duplicates are not used,
		 * so if the file is already present, the system will skip it.
		 */
		if (!$_file) {
			
			/**
			 * Interesting note here. The stream is actually the one pointing to the uploaded file,
			 * since we technically break that with writeStream
			 *
			 * @var Stream
			 */
			$stream = storage()->writeStream(
				$filename,
				$stream
			);
			
			$stream->rewind();
			
			$animated = Animated::isAnimated($tmp);
			
			if ($animated) {
				$_tmp = tmpfile();
				
				/**
				 * Generate a poster
				 */
				$poster = sprintf('%s://%s_%s.jpeg', config('storage.writeto'), uniqid('', true), $file->getClientFilename());
				\FFMpeg\FFMpeg::create()->open($tmp)->frame(TimeCode::fromSeconds(0))->save(stream_get_meta_data($_tmp)['uri']);
				storage()->writeStream($poster, Stream::fromHandle($_tmp));
				
				/**
				 * Generate the LQIP
				 */
				$lqip = LQIP::generate($_tmp);
			}
			else {
				$poster = $filename;
				$lqip = LQIP::generate($tmp);
			}
			
			/**
			 * @var FileModel
			 */
			$_file = db()->create(FileModel::class);
			$_file->setFilename($filename);
			$_file->setMD5($md5);
			$_file->setLength($stream->getSize());
			$_file->setContentType($mime);
			$_file->setAnimated($animated);
			$_file->setPoster($poster);
			$_file->setLQIP($lqip);
			$_file->store();
		}
		
		/**
		 *
		 * @var UploadModel
		 */
		$upload = db()->create(UploadModel::class);
		$upload->setFile($_file);
		$upload->initSecret();
		$upload->store();
		
		#Make the link expire in 20 minutes.
		$expires = time() + 1200;
		$salt    = bin2hex(random_bytes(10));
		$transforms = QueryString::encode([
			'width' => 700,
			'fit' => 'scale-down'
		], ',');
		
		$hash = sha1(implode('.', [
			$upload->getId(),
			$expires,
			$transforms,
			$salt,
			config('figure.publish.secret')
		]));
		
		return response(
			view(null, [
				'id' => $upload->getId(),
				'secret' => $upload->getSecret(),
				'preview' => url()->to(
					[ImageController::class, 'retrieve'],
					[
						'id' => $upload->getId(),
						'expiration' => $expires,
						'transform' => $transforms,
						'salt' => $salt,
						'hash' => $hash
					]
				),
				'meta' => [
					'animated' => $_file->getAnimated(),
					'contentType' => $_file->getContentType(),
					'length' => $_file->getLength()
				],
				'lqip' => $_file->getLQIP()
			]),
			200,
			[
				'Content-type' => 'application/json',
				'X-Filename' => $filename,
				'X-Checksum' => $md5
			]
		);
	}
}