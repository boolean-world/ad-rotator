<?php

namespace App\Library;

use App\Library\Configuration;

class LoginRateLimiter {
	protected $redis;
	protected $requests;
	protected $timeframe;
	const prefix = 'adrotate:rl_login:';

	public function __construct(\Redis $redis, int $requests, int $timeframe) {
		$this->redis = $redis;
		$this->requests = $requests;
		$this->timeframe = $timeframe;
	}

	protected function getKey(string $ip) {
		$key = bin2hex(@inet_pton($ip));

		if ($key === false) {
			throw new \InvalidArgumentException('Invalid IP address');
		}

		if (strlen($key) === 8) { // IPv4
			return 'adrotate:rl_login:4_'.$key;
		}

		// IPv6 /112 subnet
		return 'adrotate:rl_login:6_'.substr($key, 0, -4);
	}

	public function increment(string $ip) {
		$key = $this->getKey($ip);
		$this->redis->incr($key);

		if ($this->redis->ttl($key) === -1) {
			$this->redis->setTimeout($key, $this->timeframe);
		}
	}

	public function isAllowed(string $ip) {
		$key = $this->getKey($ip);
		return ($this->redis->get($key) < $this->requests);
	}

	public function reset(string $ip) {
		$key = $this->getKey($ip);
		$this->redis->del($key);
	}
}
