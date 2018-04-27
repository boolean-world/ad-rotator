<?php

namespace App\Controllers;

use App\Library\Controller;
use App\Models\RememberToken;
use App\Library\Configuration;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

class LogoutController extends Controller {
	use \App\Middlewares\Session;
	use \App\Middlewares\PersistLogin;
	use \App\Middlewares\CsrfProtected;
	use \App\Middlewares\RedirectIfLoggedOut;

	protected $middlewares = [
		'manageSession',
		'persistLogin',
		'redirectIfLoggedOut',
		'csrfProtected'
	];

	public function run(Request $request, array &$data) {
		$this->session->destroy();

		$config = $this->container->get(Configuration::class);
		$basepath = $config->get('environment.basepath', '');

		$response = new RedirectResponse("$basepath/login");
		$token = $request->cookies->get('ar_remembertoken');

		if ($token !== null) {
			RememberToken::where('authenticator', hash('sha256', $token))->delete();
			$response->headers->clearCookie('ar_remembertoken');
		}

		return $response;
	}
}
