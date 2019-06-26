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

		return new OrderManager(
			$client,
			$dw_options['handle'],
			new WordpressPostMetaData(),
			new WordpressTranslator()
		);
	}
}