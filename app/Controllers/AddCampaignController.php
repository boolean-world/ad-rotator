<?php

namespace App\Controllers;

use App\Models\Campaign;
use App\Library\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class AddCampaignController extends Controller {
	use \App\Middlewares\Session;
	use \App\Middlewares\PersistLogin;
	use \App\Middlewares\CsrfProtected;
	use \App\Middlewares\RedirectIfLoggedOut;

	protected $xhrController = true;
	protected $middlewares = [
		'manageSession',
		'persistLogin',
		'redirectIfLoggedOut',
		'csrfProtected'
	];

	public function run(Request $request, array &$data) {
		$name = trim($request->request->get('name'));

		if ($name === '') {
			return new JsonResponse([
				'text' => 'The name cannot be empty.'
			], 400);
		}

		if (strpos($name, ',') !== false) {
			return new JsonResponse([
				'text' => 'You cannot use a comma in the campaign name.'
			], 400);
		}

		if (Campaign::where('campaign_name', $name)->count() !== 0) {
			return new JsonResponse([
				'text' => 'This campaign name is already under use.'
			], 400);
		}

		$campaign = Campaign::create([
			'campaign_name' => $name
		]);

		return new JsonResponse([
			'id' => $campaign->id,
			'name' => $name
		]);
	}
}
