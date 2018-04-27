<?php

namespace App\Commands;

use App\Library\Configuration;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MigrationsInstallCommand extends Command {
	protected function configure() {
		$this->setName('migrations:install')
		     ->setDescription('Initialize the database with the necessary tables')
		     ->setHelp('Initialize the database with the necessary tables');
	}

	protected function execute(InputInterface $input, OutputInterface $output) {
		$config = new Configuration();
		$migrations = [
			'CampaignMigration',
			'AdsMigration',
			'StatsMigration',
			'RememberTokenMigration'
		];

		$output->writeln("Installing migrations...");

		foreach ($migrations as $migration) {
			$migration_class = "\\App\\Migrations\\$migration";
			$migration = new $migration_class;
			$migration->create();
		}

		$output->writeln("All migrations were run successfully.");
	}
}
