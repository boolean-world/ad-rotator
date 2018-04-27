<?php

namespace App\Controllers;

use App\Library\Controller;
use App\Library\Configuration;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class OtherSettingsChangeController extends Controller {
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
		$ignored_regex = $request->request->get('ignored_regex');
		$redirect_basepath = $request->request->get('redirect_basepath');
		$wordpress_widget_name = $request->request->get('wordpress_widget_name');

		if (!preg_match('/^[a-z_][a-z0-9_]+$/i', $wordpress_widget_name)) {
			return new JsonResponse([
				'text' => 'Invalid widget name.'
			], 400);
		}

		$wordpress_campaigns = implode(',',
			array_map(function($value) {
				return trim($value);
			}, explode(',', $request->request->get('wordpress_campaigns')))
		);

		$ret = true;

		if ($ignored_regex !== '') {
			$regex = '/'.str_replace('/', '\/', $ignored_regex).'/';
			$ret = preg_match($regex, '');
		}

		if ($ret === false) {
			return new JsonResponse([
				'text' => 'The regular expression is invalid.'
			], 400);
		}

		$config = $this->container->get(Configuration::class);
		$config->set('prefs.ignored_user_agents', $ignored_regex);
		$config->set('prefs.wordpress_campaigns', $wordpress_campaigns);
		$config->set('prefs.redirect_basepath', $redirect_basepath);
		$config->set('prefs.wordpress_widget_name', $wordpress_widget_name);
		$config->save();

		return new JsonResponse();
	}
}
