<?php

declare(strict_types=1);

namespace Dation\Woocommerce\Adapter;

use Dation\Woocommerce\RestApiClient\RestApiClient;

/**
 * The RestApiClientFactory is a service to create an instance of the RestApiClient based on the Wordpress settings
 */
class RestApiClientFactory {

    private static $client;

    public static function getClient(): RestApiClient {
        if (null === self::$client) {
            self::$client = self::constructClient();
        }

        return self::$client;
    }

    private static function constructClient(): RestApiClient {
        global $dw_options;

        return new RestApiClient($dw_options['api_key'], $dw_options['handle']);
    }
}