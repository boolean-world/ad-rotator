<?php

namespace App\Middlewares;

trait PreventCaching {
	protected function preventCaching() {
		$response = $this->next();
		$response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate');
		$response->headers->set('Pragma', 'no-cache');
		$response->headers->set('Expires', 0);
		return $response;
	}
}
