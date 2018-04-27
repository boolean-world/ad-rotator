<?php

namespace App\Controllers;

use App\Library\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class NotFoundController extends Controller {
	public function run(Request $request, array &$data) {
		$twig = $this->container->get(\Twig_Environment::class);
		return new Response($twig->render('notfound.twig'), 404);
	}
}
