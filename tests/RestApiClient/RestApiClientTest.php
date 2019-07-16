<?php

declare(strict_types=1);

use Dation\Woocommerce\Model\CourseInstance;
use Dation\Woocommerce\Model\Invoice;
use Dation\Woocommerce\Model\Payment;
use Dation\Woocommerce\Model\PaymentParty;
use Dation\Woocommerce\Model\Student;
use Dation\Woocommerce\RestApiClient\RestApiClient;
use Dation\Woocommerce\Model\Enrollment;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class RestApiClientTest extends TestCase {

	/** @var \Faker\Generator */
	protected $faker;

	public function setUp(): void {
		$this->faker = Faker\Factory::create();
	}

	public function testPostStudent(): void  {
		$student = (new Student())->setLastName($this->faker->lastName);
		$newId = $this->faker->randomNumber();

		$mockHttpClient = $this->mockGuzzle([
			new Response(201, [], json_encode(['id' => $newId])),
		]);

		$client = new RestApiClient($mockHttpClient);

		$newStudent = $client->postStudent($student);

		$this->assertEquals($newId, $newStudent->getId());
	}

	public function testGetCourseInstance(): void {
		$id = $this->faker->randomNumber();
		$name = $this->faker->word;

		$mockHttpClient = $this->mockGuzzle([
			new Response(200, [], json_encode([
				'id' => $id,
				'name' => $name,
 			]))
		]);

		$client = new RestApiClient($mockHttpClient);
		/** @var CourseInstance $courseInstance */
		$courseInstance = $client->getCourseInstance($id);

		$this->assertEquals($id, $courseInstance->getId());
		$this->assertEquals($name, $courseInstance->getName());
	}

	public function testGetCourseInstances(): void {
		$mockCourses = [
			[
				'id'   => $this->faker->randomNumber(),
				'name' => $this->faker->word,
			],
			[
				'id'   => $this->faker->randomNumber(),
				'name' => $this->faker->word,
			],
		];

		$mockHttpClient = $this->mockGuzzle([
			new Response(200, [], json_encode($mockCourses))
		]);

		$client = new RestApiClient($mockHttpClient);

		$courseInstances = $client->getCourseInstances();

		$this->assertEquals($mockCourses[0]['id'], $courseInstances[0]['id']);
		$this->assertEquals($mockCourses[0]['name'], $courseInstances[0]['name']);

		$this->assertEquals($mockCourses[1]['id'], $courseInstances[1]['id']);
		$this->assertEquals($mockCourses[1]['name'], $courseInstances[1]['name']);
	}

	public function testPostPayment() {
		$payer   = (new PaymentParty())->setType(PaymentParty::TYPE_STUDENT)->setId($this->faker->randomNumber());
		$payee   = (new PaymentParty())->setType(PaymentParty::TYPE_BANK)->setId(1);
		$payment = (new Payment())
			->setPayer($payer)
			->setPayer($payee)
			->setAmount($this->faker->randomFloat(2));

		$newPaymentId = $this->faker->randomNumber();

		$mockHttpClient = $this->mockGuzzle([
			new Response(201, [], json_encode([
				'id' => $newPaymentId
			])),
		]);

		$client     = new RestApiClient($mockHttpClient);

		$client->postPayment($payment);
	}

	public function testBillEnrollment() {
		$enrollment = (new Enrollment())->setId($this->faker->randomNumber());

		$mockHttpClient = $this->mockGuzzle([
			new Response(200, [], json_encode([
				[
					"id" => 150
				],
			])),
		]);

		$client =  new RestApiClient($mockHttpClient);

		$result = $client->billEnrollment($enrollment);

		$this->assertInstanceOf(Invoice::class, $result[0]);

	}

	private function mockGuzzle(array $responseQueue): HttpClient {
		$handler = new MockHandler($responseQueue);
		$stack = HandlerStack::create($handler);
		return new HttpClient(['handler' => $stack]);
	}
}
