<?php

namespace App\Controllers;

use App\Models\Ads;
use App\Library\Controller;
use Illuminate\Database\QueryException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class DeleteAdvertController extends Controller {
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
		$res = Ads::where('id', $data['id'])->delete();
		return new JsonResponse();
	}
}
