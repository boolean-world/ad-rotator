<?php

namespace App\Migrations;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Capsule\Manager as Capsule;

class AdsMigration extends Migration {
	public function create() {
		Capsule::schema()->create('ads', function(Blueprint $table) {
			$table->bigIncrements('id');
			$table->unsignedBigInteger('campaign_id');
			$table->string('image_url');
			$table->string('link_url');
			$table->string('slug');

			$table->index(['id', 'image_url', 'link_url']);
			$table->unique('slug');
			$table->foreign('campaign_id')->references('id')->on('campaigns');
		});
	}

	public function delete() {
		Capsule::schema()->dropIfExists('ads');
	}
}
