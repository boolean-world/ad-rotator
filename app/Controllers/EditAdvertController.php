<?php

namespace App\Controllers;

use App\Models\Ads;
use App\Library\Controller;
use Illuminate\Database\QueryException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class EditAdvertController extends Controller {
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
		$id = (int)$request->request->get('id');
		$image_url = $request->request->get('image_url');
		$link_url = $request->request->get('link_url');
		$slug = $request->request->get('slug');

		$ad = Ads::where('id', $id)->first();
		if ($ad === null) {
			return new JsonResponse([
				'text' => 'This ad does not exist.'
			], 404);
		}

		$ad->image_url = $image_url;
		$ad->link_url = $link_url;
		$ad->slug = $slug;

		try {
			$ad->save();
			return new JsonResponse([], 200);
		}
		catch (QueryException $e) {
			return new JsonResponse([
				'text' => 'An ad with this slug already exists.'
			], 400);
		}
	}
}
