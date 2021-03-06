<?php

namespace bomi\mvcat\i18n;

class I18NService {
	private $_values;

	public function __construct(string $path, array $globals) {
		$this->_values = array_merge($globals, ($path === "" ? array () : $this->_findValues($path)));
	}
		
	public function get(string $key, array $args = []) {
		if (!key_exists($key, $this->_values)) {
			return $key;
		}
		
		$string = $this->_values[$key];
		for ($i = 0; $i < sizeof($args); $i++) {
			$string = str_replace("{" . ($i + 1) . "}", trim($args[$i]), $string);
		}
		return $string;
	}
	
	private function _findValues(string $path): array {
		return file_exists($path) ? $this->_parse(file_get_contents($path)) : array();
	}

	private function _parse($content): array {		
		$content = preg_replace("/\/\*[\s\S]*?\*\/|([^:]|^)\/\/.*$/im", "", $content);
		$content = preg_replace('/^\h*\v+/m', '',$content);
		
		$values = array();
		if (preg_match_all('/([a-zA-Z0-9\-_\. ]*)=([^\r\n]*)/u', $content, $matches, PREG_SET_ORDER)) {
			foreach ($matches as $gr) {
				$values[trim($gr[1])] = trim($gr[2]);
			}
		}
		return $values;
	}
}

