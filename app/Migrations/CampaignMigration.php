<?php

namespace App\Migrations;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Capsule\Manager as Capsule;

class CampaignMigration extends Migration {
	public function create() {
		Capsule::schema()->create('campaigns', function(Blueprint $table) {
			$table->bigIncrements('id');
			$table->string('campaign_name');

			$table->unique('campaign_name');
		});
	}

	public function delete() {
		Capsule::schema()->dropIfExists('campaigns');
	}
}
