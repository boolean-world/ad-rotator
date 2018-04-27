<?php

namespace App\Controllers;

use App\Models\Ads;
use App\Library\Json;
use App\Models\Campaign;
use App\Library\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Database\Capsule\Manager as DB;

class EditCampaignController extends Controller {
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
		$id = $data['id'];

		return new Response($twig->render('edit_campaign.twig', [
			'campaign_items' => Json::dumpForHtml(Ads::orderBy('id')
				->select('id', 'image_url', 'link_url', 'slug')
				->where('campaign_id', $id)
				->get()),
			'campaign_name' => Campaign::where('id', $id)
				->pluck('campaign_name')->first(),
			'stats_link' => "stats/$id"
		]));
	}
}
