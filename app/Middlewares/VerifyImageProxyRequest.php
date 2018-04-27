<?php

namespace App\Middlewares;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

trait VerifyImageProxyRequest {
	protected function verifyImageProxyRequest(Request $request) {
		$referer = $request->headers->get('Origin') ?? $request->headers->get('Referer');
		$requested_url = $request->query->get('url');

		if (!(str_begins_with($referer, 'https://') && str_begins_with($requested_url, 'http://'))) {
			return new JsonResponse([
				'text' => 'Invalid URL or proxying is not required.'
			], 403);
		}

		return $this->next();
	}
}
