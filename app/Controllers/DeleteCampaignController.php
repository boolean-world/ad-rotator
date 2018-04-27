<?php

namespace App\Controllers;

use App\Models\Ads;
use App\Models\Stats;
use App\Models\Campaign;
use App\Library\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class DeleteCampaignController extends Controller {
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
		Ads::where('campaign_id', $data['id'])->delete();
		Stats::where('campaign_id', $data['id'])->delete();
		Campaign::where('id', $data['id'])->delete();
		return new JsonResponse();
	}
}
