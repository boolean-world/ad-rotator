<?php

namespace App\Controllers;

use App\Models\Campaign;
use App\Library\Controller;
use Symfony\Component\HttpFoundation\Request;
use Illuminate\Database\Capsule\Manager as DB;
use Symfony\Component\HttpFoundation\JsonResponse;

class SuggestCampaignController extends Controller {
	use \App\Middlewares\Session;
	use \App\Middlewares\PersistLogin;
	use \App\Middlewares\RedirectIfLoggedOut;

	protected $xhrController = true;
	protected $middlewares = [
		'manageSession',
		'persistLogin',
		'redirectIfLoggedOut'
	];

	public function run(Request $request, array &$data) {
		$name = trim($request->query->get('name'));

		if ($name === '') {
			return new JsonResponse([
				'text' => 'Cannot autocomplete an empty name.'
			]);
		}

		$name = preg_replace('/[^A-Za-z]+/', '%', "{$name}%");

		return new JsonResponse(
			Campaign::where('campaign_name', 'like', $name)->take(5)->pluck('campaign_name')
		);
	}
}
