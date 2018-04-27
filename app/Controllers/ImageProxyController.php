<?php

namespace App\Controllers;

use GuzzleHttp\Client;
use App\Models\Campaign;
use App\Library\Controller;
use App\Library\Configuration;
use GuzzleHttp\Exception\TransferException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ImageProxyController extends Controller {
	use \App\Middlewares\Session;
	use \App\Middlewares\PersistLogin;
	use \App\Middlewares\RedirectIfLoggedOut;
	use \App\Middlewares\VerifyImageProxyRequest;

	protected $xhrController = true;
	protected $middlewares = [
		'manageSession',
		'persistLogin',
		'redirectIfLoggedOut',
		'verifyImageProxyRequest'
	];

	public function run(Request $request, array &$data) {
		$cache_dir = __DIR__.'/../../data/tmp/img';
		$url = $request->query->get('url');
		$url_hash = hash('sha256', $url);
		$image_dir = $cache_dir.'/'.substr($url_hash, 0, 2);
		$image_path = $image_dir.'/'.substr($url_hash, 2);

		$fs = $this->container->get(Filesystem::class);
		$stat = @stat($image_path);

		if ($stat === false || time() - $stat[9] > 86400 || $stat[7] === 0) {
			if (!$fs->exists($image_dir)) {
				$fs->mkdir($image_dir);
			}

			$client = $this->container->get(Client::class);

			try {
				$fp = fopen($image_path, 'w');
				$response = $client->request('GET', $url, [
					'stream' => true
				]);

				$mimetype = preg_replace('/[; ].*/', '', $response->getHeader('Content-Type')[0] ?? '');
				$body = $response->getBody();

				if (!str_begins_with($mimetype, 'image/')) {
					throw new \RuntimeException;
				}

				while (!$body->eof()) {
					fwrite($fp, $body->read(4096));
				}

				$body->close();
			}
			catch (TransferException $e) {
				$fs->remove($image_path);
				return new Response('Failed to retreive object.', 404);
			}
			catch (\RuntimeException $e) {
				$fs->remove($image_path);
				return new Response('Rejecting request for illegal object type.', 403);
			}
		}
		else {
			$finfo = $this->container->get(\finfo::class);
			$mimetype = $finfo->file($image_path);
		}

		$headers = [
			'Content-Type' => $mimetype
		];

		$config = $this->container->get(Configuration::class);
		$basepath = $config->get('environment.basepath', '');

		if (substr($_SERVER['SERVER_SOFTWARE'], 0, 5) === 'nginx') {
			$headers['X-Accel-Redirect'] = "$basepath/imageproxy_assets/".substr($url_hash, 0, 2).
											'/'.substr($url_hash, 2);
			$content = '';
		}
		else {
			$content = file_get_contents($image_path);
		}

		return new Response($content, 200, $headers);
	}
}
