<?php

return (function() {
	$containerBuilder = new DI\ContainerBuilder();

	$config = new App\Library\Configuration();

	if ($config->get('environment.phase') === 'production') {
		$containerBuilder->enableCompilation(__DIR__.'/../data/cache/php-di');
	}

	$containerBuilder->addDefinitions([
		App\Library\Configuration::class => $config,

		finfo::class => function() {
			return new finfo(FILEINFO_MIME_TYPE);
		},

		Redis::class => function(App\Library\Configuration $config) {
			$redis = new Redis();
			$redis->connect($config->get('redis.socket'));
			return $redis;
		},

		GuzzleHttp\Client::class => function() {
			return new GuzzleHttp\Client([
				'timeout' => 5,
				'headers' => [
					'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/65.0.3325.181 Safari/537.36'
				],
				'allow_redirects' => [
					'max_redirects' => 2
				]
			]);
		},

		App\Library\LoginRateLimiter::class => function(Redis $redis, App\Library\Configuration $config) {
			$attempts = $config->get('prefs.login_security.attempts', 5);
			$in = $config->get('prefs.login_security.in', 600);
			return new App\Library\LoginRateLimiter($redis, $attempts, $in);
		},

		Twig_Environment::class => function(App\Library\Configuration $config) {
			$fs_loader = new Twig_Loader_Filesystem(__DIR__.'/../resources/views/');
			$twig = new Twig_Environment($fs_loader);

			if ($config->get('environment.phase') === 'production') {
				$twig->setCache(__DIR__.'/../data/cache/twig/');
			}

			$twig->addFunction(new Twig_SimpleFunction('dump', function($var) {
				dump($var);
			}));

			$twig->addFunction(new Twig_SimpleFunction('config', function($key) use ($config) {
				return $config->get($key);
			}));

			$twig->addFunction(new Twig_SimpleFunction('asset', function($path) use ($config) {
				$basepath = $config->get('environment.basepath', '');
				return "$basepath/assets/$path";
			}));

			$twig->addFunction(new Twig_SimpleFunction('link', function($path, $arr=[]) use ($config) {
				$basepath = $config->get('environment.basepath', '');
				return url("$basepath/$path", $arr);
			}));

			$twig->addFunction(new Twig_SimpleFunction('url', function($path, $arr=[]) {
				return url($path, $arr);
			}));

			$twig->addFunction(new Twig_SimpleFunction('month_in_words', function($num) {
				$months = [
					1 => 'January', 'February', 'March', 'April', 'May', 'June',
					'July', 'August', 'September', 'October', 'November', 'December'
				];

				return $months[$num] ?? false;
			}));

			return $twig;
		}
	]);

	return $containerBuilder->build();
})();
