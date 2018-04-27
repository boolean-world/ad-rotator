<?php

namespace App\Controllers;

use App\Models\Ads;
use App\Library\Json;
use App\Models\Stats;
use App\Models\Campaign;
use App\Library\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Database\Capsule\Manager as DB;

class IndexController extends Controller {
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
		return new Response($twig->render('index.twig', [
			'campaigns' => Json::dumpForHtml(Campaign::orderBy('id')->get(), JSON_HEX_TAG),
			'clicks' => Json::dumpForHtml(Stats::groupBy('campaign_id')
					->select(DB::raw('campaign_id, sum(conversions) as clicks'))
					->pluck('clicks', 'campaign_id'), JSON_FORCE_OBJECT),
			'ads' => Json::dumpForHtml(Ads::groupBy('campaign_id')
					->select(DB::raw('campaign_id, count(*) as adnum'))
					->pluck('adnum', 'campaign_id'), JSON_FORCE_OBJECT)
		]));
	}
}
