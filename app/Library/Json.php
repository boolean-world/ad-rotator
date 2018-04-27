<?php

namespace App\Library;

use App\Exceptions\JsonParseException;

class Json {
	const tokenizer_regex = '#
		[{}:,[\]]|
		[+-]?[0-9]+(?:\.[0-9]+)?(?:[Ee][+-]?[0-9]+)?|
		"[^"\\\]*(?:\\\.[^"\\\]*)*"|
		true|
		false|
		null|
		//[^\n]+\n|
		/\*.*?\*/|
		\s+
	#sx';

	public static function parse($str) {
		if (!preg_match_all(self::tokenizer_regex, $str, $tokens)) {
			throw new JsonParseException;
		}

		$expected_length = strlen($str);
		$compliant_json = '';

		foreach ($tokens[0] as $token) {
			// ignore comments and whitespace
			if ($token[0] === '/' || ctype_space($token[0])) {
				$expected_length -= strlen($token);
				continue;		
			}

			$compliant_json .= $token;
		}

		if (strlen($compliant_json) !== $expected_length) {
			throw new JsonParseException;
		}

		$result = json_decode($compliant_json, TRUE);

		if ($result === null) {
			throw new JsonParseException;
		}

		return $result;
	}

	public static function dump($args, int $bitmask = 0) {
		return json_encode($args, $bitmask | JSON_UNESCAPED_SLASHES);
	}

	public static function dumpForHtml($args, int $bitmask = 0) {
		return json_encode($args, JSON_HEX_QUOT | JSON_HEX_TAG | $bitmask);
	}
}
