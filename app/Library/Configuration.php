<?php

namespace App\Library;

class Configuration {
	private $config;
	private $cacheFile;
	private $configFile;

	public function __construct() {
		$this->cacheFile = __DIR__.'/../../data/cache/config';
		$this->configFile = __DIR__.'/../../data/config.json';

		if (file_exists($this->cacheFile)) {
			$res = require $this->cacheFile;

			if (is_array($res)) {
				$this->config = $res;
			}
		}

		if (!isset($this->config)) {
			$fp = fopen($this->configFile, 'r');

			if ($fp === false) {
				throw new \RuntimeException('Configuration file not readable');
			}

			flock($fp, LOCK_SH);
			$contents = '';

			while (($str = fgets($fp, 1024)) !== false) {
				$contents .= $str;
			}

			flock($fp, LOCK_UN);
			fclose($fp);

			$this->config = Json::parse($contents);

			if (($this->config['environment']['phase'] ?? null) === 'production') {
				file_put_contents($this->cacheFile, '<?php return '.var_export($this->config, true).';');
			}
		}
	}

	public function get($key) {
		$throw_exception = (func_num_args() < 2);
		$ref = &$this->config;

		foreach (explode('.', $key) as $segment) {
			if (!isset($ref[$segment])) {
				if ($throw_exception) {
					throw new \RuntimeException("Configuration '$key' does not exist.");
				}

				return func_get_arg(1);
			}

			$ref = &$ref[$segment];
		}

		return $ref;
	}

	public function set($key, $value) {
		$ref = &$this->config;
		$segments = explode('.', $key);
		$last_segment = $segments[count($segments) - 1];

		foreach (array_slice($segments, 0, -1) as $segment) {
			if (!isset($ref[$segment])) {
				$ref[$segment] = null;
			}

			$ref = &$ref[$segment];
		}

		$ref[$last_segment] = $value;
	}

	public function save() {
		$fp = fopen($this->configFile, 'a');
		flock($fp, LOCK_EX);

		ftruncate($fp, 0);
		fwrite($fp, Json::dump($this->config, JSON_PRETTY_PRINT));

		flock($fp, LOCK_UN);
		fclose($fp);

		@unlink($this->cacheFile);
	}
}
