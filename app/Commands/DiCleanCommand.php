<?php

namespace App\Commands;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DiCleanCommand extends Command {
	protected function configure() {
		$this->setName('clean:di')
		     ->setDescription('Remove compiled DI container')
		     ->setHelp('Remove compiled DI container (that is built in production mode)');
	}

	protected function execute(InputInterface $input, OutputInterface $output) {
		$fs = new Filesystem();
		$path = 'data/cache/php-di';

		if ($fs->exists($path)) {
			$fs->remove(glob("$path/*"));
		}

		$output->writeln('Compiled DI container removed.');
	}
}
