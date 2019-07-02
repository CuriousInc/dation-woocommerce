<?php

declare(strict_types=1);

namespace Dation\Woocommerce\Adapter;

use Dation\Woocommerce\WordpressPostMetaData;
use Dation\Woocommerce\WordpressTranslator;

/**
 * The OrderManagerFactory is a service to create an instance of OrderManager
 */
class OrderManagerFactory {

	private static $manager;

	public static function getManager(): OrderManager {
		if(null === self::$manager) {
			self::$manager = self::constructManager();
		}

		return self::$manager;
	}

	private static function constructManager(): OrderManager {
		global $dw_options;
		$client = RestApiClientFactory::getClient();

		$options = [
			'handle' => $dw_options['handle'],
//			BankId cannot be fetched from the Api, but for 99% of the cases it is 1.
//			If there is a use case for another bankId, the field kan be added to dw-options page and set there.
			'bankId' => 1
		];

		return new OrderManager(
			$client,
			$options,
			new WordpressPostMetaData(),
			new WordpressTranslator()
		);
	}
}