<?php

declare(strict_types=1);

namespace Dation\Woocommerce\Adapter;

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
		$client = RestApiClientFactory::getClient();

		return new OrderManager($client);
	}
}