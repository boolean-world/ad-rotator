<?php

namespace App\Controllers;

use App\Models\Ads;
use App\Models\Stats;
use App\Library\Controller;
use App\Library\Configuration;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

class RedirectController extends Controller {
	public function run(Request $request, array &$data) {
		$ad = Ads::where('slug', $data['slug'])->select('campaign_id', 'link_url')->first();

		if ($ad === null) {
			$twig = $this->container->get(\Twig_Environment::class);
			return new Response($twig->render('notfound.twig', [
				'hide_homepage_link' => true
			]), 404);
		}

		$config = $this->container->get(Configuration::class);
		$regex = $config->get('prefs.ignored_user_agents', null);
		$regex = '/'.str_replace('/', '\/', $regex).'/si';

		if ($regex === '//si' || !preg_match($regex, $request->headers->get('User-Agent'))) {
			$year = (int)date('Y');
			$month = (int)date('m');

			$stats = Stats::where('year', $year)
						  ->where('month', $month)
						  ->where('campaign_id', $ad->campaign_id)
						  ->first();

			if ($stats === null) {
				Stats::create([
					'year' => $year,
					'month' => $month,
					'campaign_id' => $ad->campaign_id,
					'conversions' => 1
				]);
			}
			else {
				$stats->conversions++;
				$stats->save();
			}
		}

		return new RedirectResponse($ad->link_url, 302);
	}
}