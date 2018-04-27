<?php

namespace App\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CleanCommand extends Command {
	protected function configure() {
		$this->setName('clean:all')
		     ->setDescription('Clean caches and temporary data stores')
		     ->setHelp('Clean caches and temporary data stores');
	}

	protected function execute(InputInterface $input, OutputInterface $output) {
		$app = $this->getApplication();
		$emptyargs = new ArrayInput([]);

		$app->find('clean:routes')->run($emptyargs, $output);
		$app->find('clean:config')->run($emptyargs, $output);
		$app->find('clean:templates')->run($emptyargs, $output);
		$app->find('clean:di')->run($emptyargs, $output);
		$app->find('clean:tmp')->run($emptyargs, $output);
	}
}
