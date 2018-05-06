<?php

namespace App\Controllers;

use App\Models\Stats;
use App\Models\Campaign;
use App\Library\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Database\Capsule\Manager as DB;

class StatsController extends Controller {
	use \App\Middlewares\Session;
	use \App\Middlewares\PersistLogin;
	use \App\Middlewares\PreventCaching;
	use \App\Middlewares\RedirectIfLoggedOut;

	protected $middlewares = [
		'manageSession',
		'persistLogin',
		'redirectIfLoggedOut',
		'preventCaching'
	];

	public function run(Request $request, array &$data) {
		$twig = $this->container->get(\Twig_Environment::class);

		return new Response($twig->render('stats.twig', [
			'stats' => Stats::where('campaign_id', $data['id'])
				->select('year', 'month', 'conversions')->get()->toArray(),
			'campaign_name' => Campaign::where('id', $data['id'])
				->pluck('campaign_name')->first(),
			'edit_link' => "edit/{$data['id']}"
		]));
	}
}
