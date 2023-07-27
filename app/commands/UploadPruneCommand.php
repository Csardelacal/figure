<?php namespace app\commands;

use app\models\UploadModel;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
	name: 'prune:uploads',
	description: 'Deletes unclaimed uploads'
)]
class UploadPruneCommand extends Command
{
	
	public function configure()
	{
		$this->addOption(
			'older-than',
			null,
			InputOption::VALUE_OPTIONAL, 'How long unclaimed items are kept', '1 WEEK');
	}
	
	public function execute(InputInterface $input, OutputInterface $output) : int
	{
		$now = Carbon::now();
		$expires = (CarbonInterval::createFromDateString($input->getOption('older-than')))->convertDate($now, true);
		
		
		/**
		 * @todo We need a mechanism to retrieve blocks of results at a time (like
		 * a cursor) which lazily feeds the application with results.
		 * 
		 * @todo For some reason the query builder won't work correctly with the app
		 * instead of app_id. That seems to be a bug in Spitfire
		 */
		db()->from(UploadModel::class)
			->where('created', '<', $expires->getTimestamp())
			->where('app_id', null)
			->range(0, 50000)
			->each(function (UploadModel $expired) {
				$expired->delete();
			});
		
		
		return Command::SUCCESS;
	}
	
}
