<?php

/*
 * Define your command line interface commands in here, when invoking the appropriate
 * command, Spitfire will call the director you requested.
 */

use app\commands\ApplicationTokenGenerateCommand;
use app\commands\ApplicationTokenRevokeCommand;
use app\commands\CacheClearCommand;
use app\commands\FilePruneCommand;
use app\commands\TrashPruneCommand;
use app\commands\UploadPruneCommand;
use Psr\Container\ContainerInterface;
use spitfire\contracts\core\kernel\ConsoleKernelInterface;

return function (ContainerInterface $container, ConsoleKernelInterface $kernel) {
	$kernel->register($container->get(CacheClearCommand::class));
	$kernel->register($container->get(UploadPruneCommand::class));
	$kernel->register($container->get(TrashPruneCommand::class));
	$kernel->register($container->get(FilePruneCommand::class));
	$kernel->register($container->get(ApplicationTokenGenerateCommand::class));
	$kernel->register($container->get(ApplicationTokenRevokeCommand::class));
};
