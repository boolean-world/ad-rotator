<?php

namespace App\Middlewares;

use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;

trait Session {
	protected $session;

	protected function manageSession(Request $request) {
		$this->session = $this->container->get(\App\Library\Session::class);
		$before_sessionid = $request->cookies->get('ar_sessionid');

		$this->session->bind($before_sessionid);

		$response = $this->next();
		$after_sessionid = $this->session->getId();

		// the session ID was modified.
		if ($after_sessionid !== $before_sessionid) { 
			// the session was expired.
			if ($after_sessionid === null && $before_sessionid !== null) {
				$response->headers->clearCookie('ar_sessionid');
			}
			// the session was initiated/regenerated.
			else {
				$response->headers->setCookie(new Cookie('ar_sessionid', $after_sessionid));
			}
		}

		if ($after_sessionid !== null) {
			$this->session->save();
		}

		return $response;
	}
}
