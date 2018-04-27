<?php

namespace App\Middlewares;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

trait VerifyAdvertParameters {
	protected function verifyAdvertParameters(Request $request) {
		$image_url = $request->request->get('image_url');
		$link_url = $request->request->get('link_url');
		$slug = $request->request->get('slug');

		if (!preg_match('/^https?:\/\/[a-z0-9.-]+/', $image_url)) {
			return new JsonResponse([
				'text' => 'The image URL is invalid.'
			], 400);
		}

		if (!preg_match('/^https?:\/\/[a-z0-9.-]+/', $link_url)) {
			return new JsonResponse([
				'text' => 'The link URL is invalid.'
			], 400);
		}

		if (!preg_match('/^[a-z0-9_.-]+$/', $slug)) {
			return new JsonResponse([
				'text' => 'The slug is invalid.'
			], 400);
		}

		return $this->next();
	}
}