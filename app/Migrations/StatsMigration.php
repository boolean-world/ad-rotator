<?php

namespace App\Migrations;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Capsule\Manager as Capsule;

class StatsMigration extends Migration {
	public function create() {
		Capsule::schema()->create('stats', function(Blueprint $table) {
			$table->bigIncrements('id');
			$table->unsignedBigInteger('campaign_id');
			$table->unsignedTinyInteger('month');
			$table->unsignedSmallInteger('year');
			$table->unsignedInteger('conversions');

			$table->foreign('campaign_id')->references('id')->on('campaigns');
		});
	}

	public function delete() {
		Capsule::schema()->dropIfExists('stats');
	}
}
