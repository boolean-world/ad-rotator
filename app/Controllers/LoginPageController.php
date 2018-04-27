<?php

namespace App\Controllers;

use App\Library\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class LoginPageController extends Controller {
	use \App\Middlewares\Session;
	use \App\Middlewares\PersistLogin;
	use \App\Middlewares\RedirectIfLoggedIn;

	protected $middlewares = [
		'manageSession',
		'persistLogin',
		'redirectIfLoggedIn'
	];

	public function run(Request $request, array &$data) {
		$twig = $this->container->get(\Twig_Environment::class);
		return new Response($twig->render('login.twig'));
	}
}
