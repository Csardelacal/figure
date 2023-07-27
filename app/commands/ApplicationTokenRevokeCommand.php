<?php namespace app\commands;

use app\models\ApiToken;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
	name: 'apitoken:revoke',
	description: 'Revokes a provided api token'
)]
class ApplicationTokenRevokeCommand extends Command
{
	
	public function configure()
	{
		$this->addArgument(
			'token',
			InputArgument::REQUIRED,
			'Token to be revoked',
		);
	}
	
	public function execute(InputInterface $input, OutputInterface $output) : int
	{
		$token = db()->from(ApiToken::class)->where('secret', $input->getArgument('token'))->first();
		$token->delete();
		
		$output->writeln($token->getToken());
		
		return Command::SUCCESS;
	}
	
}
