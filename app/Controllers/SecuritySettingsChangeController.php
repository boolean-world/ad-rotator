<?php

namespace App\Controllers;

use App\Library\Controller;
use App\Library\Configuration;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class SecuritySettingsChangeController extends Controller {
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
		$login_attempts = (int)$request->request->get('login_attempts');
		$login_in = (int)$request->request->get('login_in');

		if ($login_attempts < 1 || $login_in < 1) {
			return new JsonResponse([
				'text' => 'The parameter values can only be positive numbers.'
			], 400);
		}

		$val = log10($login_in)/2 - log10($login_attempts);

		if ($login_attempts < 3 || $val <= 0.35 || $val >= 1.25) {
			return new JsonResponse([
				'text' => 'The parameter values may provide weak security or poor usability.'
			], 400);
		}

		$config = $this->container->get(Configuration::class);
		$config->set('prefs.login_security.attempts', $login_attempts);
		$config->set('prefs.login_security.in', $login_in);
		$config->save();

		return new JsonResponse();
	}
}
