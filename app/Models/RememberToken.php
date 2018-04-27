<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RememberToken extends Model {
	protected $table = 'remember_token';
	protected $primaryKey = 'authenticator';

	public $timestamps = false;
	public $incrementing = false;

	public static function createFor($username) {
		$token = bin2hex(random_bytes(32));

		$tokeninfo = new self();
		$tokeninfo->authenticator = hash('sha256', $token);
		$tokeninfo->username = $username;
		$tokeninfo->created_at = time();
		$tokeninfo->save();

		return $token;
	}
}
