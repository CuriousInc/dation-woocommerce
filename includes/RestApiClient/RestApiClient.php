<?php
declare(strict_types=1);

namespace Dation\Woocommerce\RestApiClient;

use DateTime;
use GuzzleHttp\Client;

class RestApiClient {

	const BASE_API_URL = 'https://dashboard.dation.nl/api/v1/';

	/**
	 * @var \GuzzleHttp\Client
	 */
	protected $httpClient;

	public function __construct(string $apiKey, string $handle) {
		$this->httpClient = new Client([
			'base_uri' => self::BASE_API_URL,
			'headers'  => [
				'Authorization'   => "Basic {$apiKey}",
				'X-Dation-Handle' => $handle
			]
		]);
	}

	/**
	 * @param DateTime|null $startDateAfter
	 * @param DateTime|null $startDateBefore
	 *
	 * @return mixed[]
	 */
	public function getCourseInstances(DateTime $startDateAfter = null, DateTime $startDateBefore = null): array {
		// Prepare query
		$query = [];
		if(null !== $startDateBefore) {
			$query['startDateBefore'] = $startDateBefore->format('Y-m-d');
		}
		if(null !== $startDateAfter) {
			$query['startDateAfter'] = $startDateAfter->format('Y-m-d');
		}

		// Send request, parse response
		$response        = $this->httpClient->get('course-instances', ['query' => $query]);
		$courseInstances = json_decode($response->getBody()->getContents(), true) ?? [];

		return $courseInstances;
	}
}