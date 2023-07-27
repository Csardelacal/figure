<?php namespace app\commands;

use app\models\ApiToken;
use app\models\App;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
	name: 'apitoken:create',
	description: 'Creates or updates the token for an app'
)]
class ApplicationTokenGenerateCommand extends Command
{
	
	public function configure()
	{
		$this->addArgument(
			'name',
			InputArgument::REQUIRED,
			'Name of the application',
		);
	}
	
	public function execute(InputInterface $input, OutputInterface $output) : int
	{
		
		$app = db()->from(App::class)->where('name', $input->getArgument('name'))
			->first(function () use ($input, $output) : App {
				$output->writeln('Application was created');
				$app = db()->create(App::class);
				$app->setName($input->getArgument('name'));
				$app->store();
				
				return $app;
			});
		
		$token = db()->create(ApiToken::class);
		$token->setApp($app);
		$token->generate();
		$token->store();
		
		$output->writeln($token->getToken());
		
		return Command::SUCCESS;
	}
	
}
