<?php

declare(strict_types=1);

namespace Dation\Woocommerce\RestApiClient;

use DateTime;
use Dation\Woocommerce\Model\Payment;
use Dation\Woocommerce\ObjectNormalizerFactory;
use Dation\Woocommerce\Model\CourseInstance;
use Dation\Woocommerce\Model\Enrollment;
use Dation\Woocommerce\Model\Student;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
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

	const BASE_HOST       = 'https://dashboard.dation.nl';
	const BASE_PATH       = '/api/v1/';
	const BASE_API_URL    = self::BASE_HOST . self::BASE_PATH;

	/**
	 * @var \GuzzleHttp\Client
	 */
	protected $httpClient;

	/**
	 * @var \Symfony\Component\Serializer\Serializer
	 */
	protected $serializer;

	public function __construct(Client $httpClient) {
		$this->httpClient = $httpClient;

		$normalizer = ObjectNormalizerFactory::getNormalizer();
		$this->serializer = new Serializer([new DateTimeNormalizer('Y-m-d'), $normalizer, new ArrayDenormalizer()], [new JsonEncoder()]);
	}

	public static function constructForKey(string $apiKey, string $baseUrl = self::BASE_API_URL) {
		return new static(new Client([
			'base_uri' => $baseUrl,
			'headers'  => [
				'Authorization' => "Basic {$apiKey}",
			]
		]));
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
	 *
	 * @throws ClientException
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

	/**
	 * @param int $courseInstanceId
	 *
	 * @return CourseInstance
	 *
	 * @throws ClientException
	 */
	public function getCourseInstance(int $courseInstanceId) {
		$response = $this->httpClient->get("course-instances/$courseInstanceId", [
			'headers' => ['Content-Type' => 'application/json'],
		]);

		return $this->serializer->deserialize($response->getBody()->getContents(), CourseInstance::class, 'json');
	}

	/**
	 * @param int $courseInstanceId
	 * @param Enrollment $enrollment
	 *
	 * @return Enrollment
	 *
	 * @throws ClientException
	 */
	public function postEnrollment(int $courseInstanceId, Enrollment $enrollment) {
		$response = $this->httpClient->post("course-instances/$courseInstanceId/enrollments", [
			'headers' => ['Content-Type' => 'application/json'],
			'body' => $this->serializer->serialize($enrollment, 'json')
		]);

		return $this->serializer->deserialize(
			$response->getBody()->getContents(),
			Enrollment::class,
			'json',
			['object_to_populate' => $enrollment]
		);
	}

	/**
	 * @param Payment $payment
	 *
	 * @return Payment
	 *
	 * @throws ClientException
	 */
	public function addPayment(Payment $payment) {
		$response = $this->httpClient->post('payments', [
			'headers' => ['Content-Type' => 'applications/json'],
			'body'    => $this->serializer->serialize($payment, 'json')
		]);

		return $this->serializer->deserialize(
			$response->getBody()->getContents(),
			Payment::class,
			'json',
			['object_to_populate' => $payment]
		);
	}
}