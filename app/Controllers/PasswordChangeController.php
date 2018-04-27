<?php

namespace App\Controllers;

use ZxcvbnPhp\Zxcvbn;
use App\Library\Controller;
use App\Library\Configuration;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class PasswordChangeController extends Controller {
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
		$config = $this->container->get(Configuration::class);
		$username = $this->session->get('username');
		$userinfo = $config->get('users');

		$old_password = $request->request->get('old_password');
		$new_password = $request->request->get('new_password');
		$new_password_again = $request->request->get('new_password_again');
		$old_password_hash = $userinfo[$username];

		if (!password_verify($old_password, $old_password_hash)) {
			return new JsonResponse([
				'text' => 'The old password is incorrect.'
			], 400);
		}

		if ($new_password === $old_password) {
			return new JsonResponse([
				'text' => 'The new password is the same as the old password.'
			], 400);
		}

		if ($new_password !== $new_password_again) {
			return new JsonResponse([
				'text' => 'You typed in different new passwords.'
			], 400);
		}

		if (mb_strlen($new_password) < 8) {
			return new JsonResponse([
				'text' => 'Please use a password with at least 8 characters.'
			], 400);
		}

		$zxcvbn = $this->container->get(Zxcvbn::class);
		$score = $zxcvbn->passwordStrength($new_password, [$username])['score'];

		if ($score === 0) {
			return new JsonResponse([
				'text' => 'Please avoid the use of common words in the password.'
			], 400);
		}

		$userinfo[$username] = password_hash($new_password, PASSWORD_DEFAULT);
		$config->set('users', $userinfo);
		$config->save();

		return new JsonResponse([
			'text' => 'Your password was changed successfully.'
		]);
	}
}
