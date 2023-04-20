<?php namespace app\controllers;

use app\models\FileModel;
use app\models\UploadModel;
use PDO;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;
use spitfire\core\http\request\handler\RouterActionRequestHandler;
use spitfire\core\Request;
use spitfire\core\Response;
use spitfire\io\stream\Stream;
use spitfire\mvc\Controller;
use spitfire\storage\database\ConnectionGlobal;

class HomeController extends Controller
{
	
	private Request $request;
	
	public function __construct(Request $request)
	{
		$this->request = $request;
	}
	
	public function index(Request $request)  : Response
	{
		/**
		 * @var FileModel
		 */
		$res = (new FileModel(new ConnectionGlobal()))->query()->where('_id', 2)->first();
		$original = $res? $res->getUpdateTimestamp() : 0;
		
		if ($res) {
			$res->setFilename('test');
			$res->store();
			
			
			$res2 = (new FileModel(new ConnectionGlobal()))->query()->where('_id', 1)->first();
			$updated = $res2->getUpdateTimestamp();
			
			$res3 = (new UploadModel(new ConnectionGlobal()))->query()->with(['file'])->where('file', '!=', null)->all();
			$updated = $res3->first()->getFile()->getId();
			
			$res3->each(function (UploadModel $e) {
				echo $e->getId() . ':' . $e->getFile()?->getId() . ':' . $e->getActiveRecord()->getUnderlyingRecord()->get('file') . PHP_EOL;
			});
		}
		
		return response(view('home', ['record' => $res, 'original' => $original, 'updated' => $updated]));
	}
	
	public function create()
	{
		$record = db()->create(FileModel::class);
		$record->setFilename('test');
		$record->setMD5(md5(''));
		$record->setLength(123);
		$record->store();
		
		$record2 = db()->create(UploadModel::class);
		$record2->setFile($record);
		$record2->store();
		
		var_dump($record);
		return response(\spitfire\io\stream\Stream::fromString('success'));
	}
}
