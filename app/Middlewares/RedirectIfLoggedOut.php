<?php

namespace App\Middlewares;

use App\Library\Configuration;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;

trait RedirectIfLoggedOut {
	protected function redirectIfLoggedOut() {
		$username = $this->session->get('username');
		$config = $this->container->get(Configuration::class);

		if ($username !== null) {
			$users = $config->get('users', []);

			if (isset($users[$username])) {
				return $this->next();
			}

			// Session carries bad data, destroy it.
			$this->session->destroy();
		}

		if ($this->xhrController ?? false) {
			return new JsonResponse([
				'code' => 'not_logged_in',
				'text' => 'Sorry, you are not logged in. Redirecting in a few seconds...'
			], 403);
		}

		$basepath = $config->get('environment.basepath', '');
		return new RedirectResponse("$basepath/login");
	}
}
