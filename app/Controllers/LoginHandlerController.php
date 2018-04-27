<?php

namespace App\Controllers;

use App\Library\Controller;
use App\Models\RememberToken;
use App\Library\Configuration;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class LoginHandlerController extends Controller {
	use \App\Middlewares\Session;
	use \App\Middlewares\PersistLogin;
	use \App\Middlewares\CsrfProtected;
	use \App\Middlewares\LoginRateLimited;
	use \App\Middlewares\RedirectIfLoggedIn;

	protected $xhrController = true;
	protected $middlewares = [
		'manageSession',
		'persistLogin',
		'redirectIfLoggedIn',
		'csrfProtected',
		'loginRateLimited'
	];

	public function run(Request $request, array &$data) {
		$username = trim($request->request->get('username'));
		$password = $request->request->get('password');

		$config = $this->container->get(Configuration::class);
		$users = $config->get('users', []);
		$hash = $users[$username] ?? null;

		if ($username === '' || mb_strlen($password) < 8 || $hash === null || !password_verify($password, $hash)) {
			return new JsonResponse([
				'text' => 'Invalid username or password.'
			], 403);
		}

		if (!$this->session->isStarted()) {
			$this->session->start();
		}
		else {
			$this->session->regenerate();
		}

		$this->session->set('username', $username);
		$time = microtime(true);
		$response = new JsonResponse();

		if ($request->request->get('remember_me')) {
			$response->headers->setCookie(new Cookie('ar_remembertoken', RememberToken::createFor($username), $time + 2592000));
		}

		return $response;
	}
}
