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
		/** @var \Dation\Woocommerce\RestApiClient\Model\CourseInstance $courseInstance */
		$courseInstance = $client->getCourseInstance($id);

		$this->assertEquals($id, $courseInstance->getId());
		$this->assertEquals($name, $courseInstance->getName());
	}

	public function testGetCourseInstances(): void {
		$id1   = $this->faker->randomNumber();
		$name1 = $this->faker->word;

		$id2   = $this->faker->randomNumber();
		$name2 = $this->faker->word;

		$mockHttpClient = $this->mockGuzzle([
			new Response(200, [], json_encode([
				[
					'id' => $id1,
					'name' => $name1
				],
				[
					'id' => $id2,
					'name' => $name2
				],
			]))
		]);

		$client = new RestApiClient($mockHttpClient);

		$courseInstances = $client->getCourseInstances();

		$this->assertEquals($courseInstances[0]['id'], $id1);
		$this->assertEquals($courseInstances[0]['name'], $name1);

		$this->assertEquals($courseInstances[1]['id'], $id2);
		$this->assertEquals($courseInstances[1]['name'], $name2);
	}

	private function mockGuzzle(array $responseQueue): HttpClient {
		$handler = new MockHandler($responseQueue);
		$stack = HandlerStack::create($handler);
		return new HttpClient(['handler' => $stack]);
	}
}