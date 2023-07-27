<?php namespace app\commands;

use app\glide\Server;
use app\glide\ServerFactory;
use app\models\FileCache;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use spitfire\io\Filesize;
use spitfire\storage\database\OrderBy;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
	name: 'cache:clear',
	description: 'Empties the cache'
)]
class CacheClearCommand extends Command
{
	
	private Server $glide;
	
	public function __construct(Server $glide)
	{
		$this->glide = $glide;
		parent::__construct();
	}
	
	/**
	 * @todo Add option to set the minimum required free space if the cache is still
	 * within spec but the drive is full regardless.
	 */
	public function configure()
	{
		$this->addOption(
			'older-than',
			null,
			InputOption::VALUE_OPTIONAL, 'How long the cache should be valid for', '1 YEAR');
			
		$this->addOption(
			'max-size',
			null,
			InputOption::VALUE_OPTIONAL, 'Maximum filesize of the cache', '100G');
	}
	
	public function execute(InputInterface $input, OutputInterface $output) : int
	{
		$now = Carbon::now();
		$expires = (CarbonInterval::createFromDateString($input->getOption('older-than')))->convertDate($now, true);
		
		
		/**
		 * @todo We need a mechanism to retrieve blocks of results at a time (like
		 * a cursor) which lazily feeds the application with results.
		 */
		db()->from(FileCache::class)
			->where('used', '<', $expires->getTimestamp())
			->range(0, 50000)
			->each(function (FileCache $expired) {
				$this->glide->deleteCacheFile($expired->getCacheFilename());
				$expired->delete();
			});
		
		$max = Filesize::parse($input->getOption('max-size'));
		
		/**
		 * See if the cache size is within constraint
		 * 
		 * @todo Future revisions would be advised to actually use a materialized cache
		 * value in redis or something that can be used to speed this up by keeping tabs
		 * how big the cache is whenever files are added or removed.
		 * @var int
		 */
		$size = db()->from(FileCache::class)->sum('size');
		
		while ($size > $max->getSize()) {
			$qb = db()->from(FileCache::class);
			$qb->getQuery()->putOrder(new OrderBy('used'));
			
			$qb->range(0, 500)->each(function (FileCache $file) use (&$size, $output) {
				$output->writeln('Before: ' . $size);
				$size-= $file->getSize();
				$file->delete();
				$output->writeln('After : ' . $size);
			});
		}
		
		return Command::SUCCESS;
	}
	
}
