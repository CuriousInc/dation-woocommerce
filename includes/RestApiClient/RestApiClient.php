<?php

declare(strict_types=1);

namespace Dation\Woocommerce\RestApiClient;

use DateTime;
use Dation\Woocommerce\Model\Invoice;
use Dation\Woocommerce\Model\Payment;
use Dation\Woocommerce\ObjectNormalizerFactory;
use Dation\Woocommerce\Model\CourseInstance;
use Dation\Woocommerce\Model\Enrollment;
use Dation\Woocommerce\Model\Student;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Symfony\Component\Serializer\Encoder\JsonEncode;
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

	public const BASE_HOST    = 'https://dashboard.dation.nl';
	public const BASE_PATH    = '/api/v1/';
	public const BASE_API_URL = self::BASE_HOST . self::BASE_PATH;

	private const DEFAULT_CONTENT_TYPE_HEADER = ['Content-Type' => 'application/json'];

	/**
	 * @var Client
	 */
	protected $httpClient;

	/**
	 * @var Serializer
	 */
	protected $serializer;

	public function __construct(Client $httpClient) {
		$this->httpClient = $httpClient;

		$normalizer = ObjectNormalizerFactory::getNormalizer();
		$this->serializer = new Serializer(
			[
				new DateTimeNormalizer('Y-m-d'),
				$normalizer, new ArrayDenormalizer()
			],
			[
				new JsonEncoder(new JsonEncode(JSON_PRESERVE_ZERO_FRACTION))
			]
		);
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
		$response = $this->httpClient->post('students', [
			'headers' => self::DEFAULT_CONTENT_TYPE_HEADER,
			'body'    => $this->serializer->serialize($student, 'json')
		]);

		return $this->serializer->deserialize(
			$response->getBody()->getContents(),
			Student::class,
			'json'
		);
	}

	/**
	 * @param int $courseInstanceId
	 *
	 * @return CourseInstance
	 *
	 * @throws ClientException
	 */
	public function getCourseInstance(int $courseInstanceId): CourseInstance {
		$response = $this->httpClient->get("course-instances/$courseInstanceId", [
			'headers' => self::DEFAULT_CONTENT_TYPE_HEADER,
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
	public function postEnrollment(int $courseInstanceId, Enrollment $enrollment): Enrollment {
		$response = $this->httpClient->post("course-instances/$courseInstanceId/enrollments", [
			'headers' => self::DEFAULT_CONTENT_TYPE_HEADER,
			'body'    => $this->serializer->serialize($enrollment, 'json')
		]);

		return $this->serializer->deserialize(
			$response->getBody()->getContents(),
			Enrollment::class,
			'json'
		);
	}

	/**
	 * @param Payment $payment
	 *
	 * @return void
	 *
	 * @throws ClientException
	 */
	public function postPayment(Payment $payment): void {
		$this->httpClient->post('payments', [
			'headers' => self::DEFAULT_CONTENT_TYPE_HEADER,
			'body'    => $this->serializer->serialize($payment, 'json')
		]);
	}

	public function billEnrollment(Enrollment $enrollment) {
		$response = $this->httpClient->put("enrollments/{$enrollment->getId()}/bill", [
			'headers' => self::DEFAULT_CONTENT_TYPE_HEADER
		]);

		return $this->serializer->deserialize(
			$response->getBody()->getContents(),
			Invoice::class,
			'json'
		);
	}
}
