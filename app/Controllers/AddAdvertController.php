<?php

namespace App\Controllers;

use App\Models\Ads;
use App\Library\Controller;
use Illuminate\Database\QueryException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class AddAdvertController extends Controller {
	use \App\Middlewares\Session;
	use \App\Middlewares\PersistLogin;
	use \App\Middlewares\CsrfProtected;
	use \App\Middlewares\RedirectIfLoggedOut;
	use \App\Middlewares\VerifyAdvertParameters;

	protected $xhrController = true;
	protected $middlewares = [
		'manageSession',
		'persistLogin',
		'redirectIfLoggedOut',
		'csrfProtected',
		'verifyAdvertParameters'
	];

	public function run(Request $request, array &$data) {
		$campaign_id = (int)$request->request->get('campaign_id');
		$image_url = $request->request->get('image_url');
		$link_url = $request->request->get('link_url');
		$slug = $request->request->get('slug');

		if (Ads::where('campaign_id', $campaign_id)
			->where('image_url', $image_url)
			->where('link_url', $link_url)
			->count() !== 0) {
			return new JsonResponse([
				'text' => 'An ad with the same image and link URL exists.'
			], 400);
		}

		if (Ads::where('slug', $slug)->count() !== 0) {
			return new JsonResponse([
				'text' => 'An ad with the same slug already exists.
			'], 400);
		}

		try {
			$ad = Ads::create(compact('campaign_id', 'image_url', 'link_url', 'slug'));
		}
		catch (QueryException $e) {
			return new JsonResponse([
				'text' => 'This campaign does not exist.'
			], 404);
		}

		return new JsonResponse([
			'id' => $ad->id
		]);
	}
}
