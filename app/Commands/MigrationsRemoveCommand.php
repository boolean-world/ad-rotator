<?php

namespace App\Commands;

use App\Library\Configuration;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MigrationsRemoveCommand extends Command {
	protected function configure() {
		$this->setName('migrations:remove')
		     ->setDescription('Remove tables from the database')
		     ->setHelp('Remove tables from the database');
	}

	protected function execute(InputInterface $input, OutputInterface $output) {
		$config = new Configuration();
		$migrations = [
			'RememberTokenMigration',
			'StatsMigration',
			'AdsMigration',
			'CampaignMigration',
		];

		$output->writeln("Removing migrations...");

		foreach ($migrations as $migration) {
			$migration_class = "\\App\\Migrations\\$migration";
			$migration = new $migration_class;
			$migration->delete();
		}

		$output->writeln("All migrations were run successfully.");
	}
}
