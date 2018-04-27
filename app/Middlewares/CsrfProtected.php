<?php

namespace App\Middlewares;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

trait CsrfProtected {
	protected function csrfProtected(Request $request) {
		$referer = $request->headers->get('Origin') ?? $request->headers->get('Referer');
		$referer_domain = preg_replace('#^https?://([^/]+)(?:/.*)?$#i', '\1', $referer);
		$host = $request->headers->get('Host');

		if ($referer_domain !== $host) {
			return new JsonResponse([
				'text' => 'Sorry, you cannot perform this action. Please ensure referers are enabled in your browser.'
			], 403);
		}

		return $this->next();
	}
}
