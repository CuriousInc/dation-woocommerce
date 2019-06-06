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
	 * Search Course Instances
	 *
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
		$transformedStudentData['dateOfBirth'] = $birthDate->format(DW_API_DATE_FORMAT);
		$transformedStudentData['issueDateCategoryBDrivingLicense '] = $issueDateDrivingLicense->format(DW_API_DATE_FORMAT);

		unset($transformedStudentData['issueDate']);

        $response = $this->httpClient->post('students', ['form_params' => $transformedStudentData, 'debug' => true]);

        return \GuzzleHttp\json_decode($response->getBody()->getContents(), true);
	}
}