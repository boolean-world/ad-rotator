<?php

namespace App\Middlewares;

use App\Library\LoginRateLimiter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

trait LoginRateLimited {
	protected function loginRateLimited(Request $request) {
		$ratelimiter = $this->container->get(LoginRateLimiter::class);
		$ip = $request->getClientIp();

		if (!$ratelimiter->isAllowed($ip)) {
			return new JsonResponse([
				'text' => 'You tried logging in too many times. Please try after some time.'
			], 403);
		}

		$ratelimiter->increment($ip);
		return $this->next();
	}
}
