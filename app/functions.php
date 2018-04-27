<?php

function str_remove_prefix($str, $prefix) {
	$len = strlen($prefix);

	if (substr($str, 0, $len) === $prefix) {
		return substr($str, $len);
	}

	return $str;
}

function str_begins_with($str, $prefix) {
	return substr($str, 0, strlen($prefix)) === $prefix;
}

function str_ends_with($str, $suffix) {
	return substr($str, -strlen($suffix)) == $suffix;
}

function url($path, $arr) {
	$rv = $path;
	$begin_query_str = false;

	foreach ($arr as $param => $value) {
		if (!$begin_query_str) {
			$rv .= '?';
			$begin_query_str = true;
		}
		else {
			$rv .= '&';
		}

		$rv .= rawurlencode($param).'='.urlencode($value);
	}

	return $rv;
}

function bootstrap_eloquent(App\Library\Configuration $config) {
	$capsule = new Illuminate\Database\Capsule\Manager();
	$capsule->addConnection($config->get('database'));
	$capsule->setAsGlobal();
	$capsule->bootEloquent();
}
