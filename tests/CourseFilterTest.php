<?php

declare(strict_types=1);

use Dation\Woocommerce\Adapter\CourseFilter;
use PHPUnit\Framework\TestCase;

class CourseFilterTest extends TestCase {
	/** @var \Faker\Generator */
	protected $faker;

	/** @var int */
	protected $idCounter = 0;

	public function setUp(): void {
		$this->faker = Faker\Factory::create();
	}

	public function courseDataProvider() {
		return [
			'no filter'                           => ['', 3, ['N18', 'VB1', null]],
			'one filter'                          => ['N18', 1, ['N18', 'VB1', null, 'XX']],
			'multiple filters - default format'   => ['N18;VB1', 2, ['N18', 'VB1', null, 'XX']],
			'multiple filters - extra ;'          => ['N18;VB1;', 2, ['N18', 'VB1', null, 'XX']],
			'multiple filters - extra spaces'     => [' N18; VB1', 2, ['N18', 'VB1', null, 'XX']],
			'multiple filters - small characters' => ['n18;vb1', 2, ['N18', 'VB1', null, 'XX']],
		];
	}

	/**
	 * @dataProvider courseDataProvider
	 *
	 * @param string|null $codeToFilter
	 * @param int $expectedResults
	 * @param array $testData
	 */
	public function testFilters(?string $codeToFilter, int $expectedResults, array $testData): void {
		$testCourses = $this->generateTestData($testData);
		$courseFilter = new CourseFilter($testCourses);

		$filteredCourses = $courseFilter->filter_courses_on_ccv_code_and_private($codeToFilter);

		$this->assertCount($expectedResults, $filteredCourses);
	}

	private function generateTestData(array $testData): array {
		$idCounter = 1;
		$courses   = [];
		foreach($testData as $data) {
				$courses[] = [
					'id'      => $idCounter,
					'ccvCode' => $data,
					'closed'  => false,
				];
				$idCounter++;
			}
		return $courses;
	}
}