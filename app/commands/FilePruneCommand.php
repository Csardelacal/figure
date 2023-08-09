<?php namespace app\commands;

use app\models\FileModel;
use League\Flysystem\FilesystemException;
use League\Flysystem\UnableToDeleteDirectory;
use Psr\Log\LoggerInterface;
use spitfire\model\query\RestrictionGroupBuilderInterface;
use spitfire\model\QueryBuilder;
use spitfire\model\QueryBuilderBuilder;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

#[AsCommand(
	name: 'prune:files',
	description: 'Marks files for deletion that are not used by any upload'
)]
class FilePruneCommand extends Command
{
	
	public function __construct(private LoggerInterface $logger)
	{
		parent::__construct();
	}
	
	public function execute(InputInterface $input, OutputInterface $output) : int
	{
		
		/**
		 * @todo We need a mechanism to retrieve blocks of results at a time (like
		 * a cursor) which lazily feeds the application with results.
		 */
		$query = db()->from(FileModel::class)->hasNo('uploads', function (QueryBuilderBuilder $builder) : QueryBuilder {
			return $builder->withTrashed()->build();
		});
		
		$query->range(0, 50000)
			->each(function (FileModel $expired) : array {
				try {
					$files = [$expired->getPoster(), $expired->getFileName()];
					$expired->delete();
					return $files;
				}
				catch (Throwable) { 
					/**
					 * @todo Add logger
					 */
					return [];
				}
			})
			->flatten()
			->each(function (string $filename) {
				try { 
					storage()->has($filename) && storage()->delete($filename); 
					$this->logger->debug(sprintf('Deleted %s', $filename));
				}
				catch (FilesystemException | UnableToDeleteDirectory) {
					$this->logger->error(sprintf('Could not delete %s', $filename));
				}
			});
		
		
		return Command::SUCCESS;
	}
	
}
