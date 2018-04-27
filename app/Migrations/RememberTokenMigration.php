<?php

namespace App\Migrations;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Capsule\Manager as Capsule;

class RememberTokenMigration extends Migration {
	public function create() {
		Capsule::schema()->create('remember_token', function(Blueprint $table) {
			$table->string('authenticator');
			$table->string('username');
			$table->unsignedBigInteger('created_at');

			$table->primary('authenticator');
		});
	}

	public function delete() {
		Capsule::schema()->dropIfExists('remember_token');
	}
}
