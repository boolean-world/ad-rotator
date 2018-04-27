<?php

namespace App\Middlewares;

use App\Library\Configuration;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;

trait RedirectIfLoggedIn {
	protected function redirectIfLoggedIn() {
		$username = $this->session->get('username');

		if ($username !== null) {
			$config = $this->container->get(Configuration::class);
			$users = $config->get('users', []);

			if (isset($users[$username])) {
				if ($this->xhrController ?? false) {
					return new JsonResponse([
						'code' => 'already_logged_in'
					], 400);
				}

				$basepath = $config->get('environment.basepath', '');
				return new RedirectResponse("$basepath/");
			}

			// Session carries bad data, destroy it.
			$this->session->destroy();
		}

		return $this->next();
	}
}
