<?php

namespace App\Controllers;

use App\Library\Controller;
use App\Library\Configuration;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SettingsController extends Controller {
	use \App\Middlewares\Session;
	use \App\Middlewares\PersistLogin;
	use \App\Middlewares\RedirectIfLoggedOut;

	protected $middlewares = [
		'manageSession',
		'persistLogin',
		'redirectIfLoggedOut'
	];

	public function run(Request $request, array &$data) {
		$twig = $this->container->get(\Twig_Environment::class);
		$config = $this->container->get(Configuration::class);
		$current_username = $this->session->get('username');

		return new Response($twig->render('settings.twig', [
			'prefs' => $config->get('prefs', []),
			'current_username' => $current_username
		]));
	}
}
