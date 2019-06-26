<?php
declare(strict_types=1);

namespace Dation\Woocommerce;

class WordpressTranslator implements TranslatorInterface {

	public function translate($message) {
		return __($message);
	}
}