<?php

declare(strict_types=1);

use Dation\Woocommerce\RestApiClient\Model\Student;
use Dation\Woocommerce\RestApiClient\RestApiClient;
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

	private function mockGuzzle(array $responseQueue): HttpClient {
		$handler = new MockHandler($responseQueue);
		$stack = HandlerStack::create($handler);
		return new HttpClient(['handler' => $stack]);
	}
}