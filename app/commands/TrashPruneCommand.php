<?php namespace app\commands;

use app\models\ApiToken;
use app\models\FileModel;
use app\models\UploadModel;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use spitfire\model\Model;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
	name: 'prune:trash',
	description: 'Permanently deletes trashed data'
)]
class TrashPruneCommand extends Command
{
	
	public function configure()
	{
		$this->addOption(
			'older-than',
			null,
			InputOption::VALUE_OPTIONAL, 'How long unclaimed items are kept', '2 WEEK');
	}
	
	public function execute(InputInterface $input, OutputInterface $output) : int
	{
		$now = Carbon::now();
		$expires = (CarbonInterval::createFromDateString($input->getOption('older-than')))->convertDate($now, true);
		
		$models = [
			UploadModel::class,
			ApiToken::class
		];
		
		/**
		 * @todo We need a mechanism to retrieve blocks of results at a time (like
		 * a cursor) which lazily feeds the application with results.
		 */
		foreach ($models as $model) {
			db()->from($model)->onlyTrashed()
				->where('removed', '<', $expires->getTimestamp())
				->range(0, 50000)
				->each(function (Model $expired) use ($model) {
					assert($expired instanceof $model);
					$expired->delete(['force' => true]);
				});
		}
		
		
		return Command::SUCCESS;
	}
	
}
