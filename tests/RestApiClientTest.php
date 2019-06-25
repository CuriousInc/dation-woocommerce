<?php

declare(strict_types=1);

use Dation\Woocommerce\Model\Student;
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
		/** @var \Dation\Woocommerce\Model\CourseInstance $courseInstance */
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

	private function mockGuzzle(array $responseQueue): HttpClient {
		$handler = new MockHandler($responseQueue);
		$stack = HandlerStack::create($handler);
		return new HttpClient(['handler' => $stack]);
	}
}