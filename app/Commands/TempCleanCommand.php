<?php

namespace App\Commands;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TempCleanCommand extends Command {
	protected function configure() {
		$this->setName('clean:tmp')
		     ->setDescription('Remove tempoary files')
		     ->setHelp('Remove tempoary files');
	}

	protected function execute(InputInterface $input, OutputInterface $output) {
		$fs = new Filesystem();
		$path = 'data/tmp';

		if ($fs->exists($path)) {
			$fs->remove(glob("$path/*"));
		}

		$output->writeln('Temporary files removed.');
	}
}
