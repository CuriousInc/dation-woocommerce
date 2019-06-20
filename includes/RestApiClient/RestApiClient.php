<?php

declare(strict_types=1);

namespace Dation\Woocommerce\RestApiClient;

use DateTime;
use Dation\Woocommerce\RestApiClient\Model\Student;
use GuzzleHttp\Client;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * The RestApiClient is a service that deals with the communication with the
 * Dation API.
 *
 * It works at the level of functional objects (e.g. students,
 * course-instances etc.) in contrast to technical details (like
 * requests, connections, etc.).
 *
 * @internal THe actual transport is delegated to a HTTP client
 */
class RestApiClient {

	public const BASE_HOST       = 'https://dashboard.dation.nl';
	public const BASE_PATH       = '/api/v1/';
	public const BASE_API_URL    = self::BASE_HOST . self::BASE_PATH;

	/**
	 * @var \GuzzleHttp\Client
	 */
	protected $httpClient;

	/**
	 * @var \Symfony\Component\Serializer\Serializer
	 */
	protected $serializer;

	public function __construct(string $apiKey, string $handle, string $baseUrl = self::BASE_API_URL) {
		$this->httpClient = new Client([
			'base_uri' => $baseUrl,
			'headers'  => [
				'Authorization'   => "Basic {$apiKey}",
				'X-Dation-Handle' => $handle
			]
		]);

		$this->serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);
	}

	/**
	 * Search Course Instances
	 *
	 * @param DateTime|null $startDateAfter
	 * @param DateTime|null $startDateBefore
	 *
	 * @return mixed[][] Array of Course Instances data (associative array's)
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

		return $this->get('course-instances', $query) ?? [];
	}

	private function get(string $endpoint, array $query) {
		$response = $this->httpClient->get($endpoint, ['query' => $query]);

		return \GuzzleHttp\json_decode($response->getBody()->getContents(), true);
	}

	/**
	 * Create a new Student
	 *
	 * @param Student $student The student data to post
	 *
	 * @return Student The same student, augmented with data returned by the API
	 */
	public function postStudent(Student $student): Student {
		$response     = $this->httpClient->post('students', [
			'headers' => ['Content-Type' => 'application/json'],
			'body' => $this->serializer->serialize($student, 'json')
		]);
		return $this->serializer->deserialize(
			$response->getBody()->getContents(),
			Student::class,
			'json',
			['object_to_populate' => $student]
		);
	}
}