<?php

namespace App\Commands;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ConfigCleanCommand extends Command {
	protected function configure() {
		$this->setName('clean:config')
		     ->setDescription('Remove compiled config file')
		     ->setHelp('Remove compiled config file built in production mode');
	}

	protected function execute(InputInterface $input, OutputInterface $output) {
		$fs = new Filesystem();
		$path = 'data/cache/config';

		if ($fs->exists($path)) {
			$fs->remove($path);
		}

		$output->writeln('Compiled configuration file removed.');
	}
}
