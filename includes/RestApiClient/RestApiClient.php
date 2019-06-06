<?php
declare(strict_types=1);

namespace Dation\Woocommerce\RestApiClient;

use DateTime;
use GuzzleHttp\Client;

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

	const BASE_HOST       = 'https://dashboard.dation.nl';
	const BASE_PATH       = '/api/v1/';
	const BASE_API_URL    = self::BASE_HOST . self::BASE_PATH;
	const API_DATE_FORMAT = 'Y-m-d';

	/**
	 * @var \GuzzleHttp\Client
	 */
	protected $httpClient;

	public function __construct(string $apiKey, string $handle, string $baseUrl = self::BASE_API_URL) {
		$this->httpClient = new Client([
			'base_uri' => $baseUrl,
			'headers'  => [
				'Authorization'   => "Basic {$apiKey}",
				'X-Dation-Handle' => $handle
			]
		]);
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

	/**
	 * Create a new Student
	 *
	 * @param mixed[] $studentData Associative array of student data, e.g. ['firstName' => 'Piet', ...]
	 *
	 * @return mixed[] Associative array of student data, as returned by the response
	 */
	public function postStudent(array $studentData): array {
		/** @var DateTime $birthDate */
		$birthDate = $studentData['dateOfBirth'];
		/** @var DateTime $issueDateDrivingLicense */
		$issueDateDrivingLicense = $studentData['issueDate'];

		$transformedStudentData = $studentData;
		$transformedStudentData['dateOfBirth'] =
			$birthDate ? $birthDate->format(self::API_DATE_FORMAT) : null;
		$transformedStudentData['issueDateCategoryBDrivingLicense '] =
			$issueDateDrivingLicense ? $issueDateDrivingLicense->format(self::API_DATE_FORMAT) : null;

		unset($transformedStudentData['issueDate']);

		return $this->post('students', $transformedStudentData);
	}

	private function get(string $endpoint, array $query) {
		$response        = $this->httpClient->get($endpoint, ['query' => $query]);

		return \GuzzleHttp\json_decode($response->getBody()->getContents(), true);
	}

	private function post(string $endpoint, array $data): array {
		$response = $this->httpClient->post($endpoint, ['form_params' => $data, 'debug' => true]);
		$responseData = \GuzzleHttp\json_decode($response->getBody()->getContents(), true);

		return \array_merge($data, $responseData);
	}
}