<?php
declare(strict_types=1);


namespace Dation\Woocommerce\Adapter;


class CourseFilter {
	/** @var array */
	private $courses;

	public function __construct(array $courses) {
		$this->courses = $courses;
	}


	public function filter_courses_on_ccv_code_and_private(?string $filters): array {
		//Filter out private courses
		$filteredCourses = array_filter($this->courses, function ($course) {
			return $course['closed'] !== true;
		});

		if($filters && $filters !== "") {
			//Filter out empty values and strings
			$codesToFilter = array_filter(explode(';', $filters), function($code) {
				return ($code && $code !== '');
			});

			//Format codes to be filtered
			$formattedCodes = array_map(function ($code) {
				return strtolower(trim($code));
			}, $codesToFilter);

			//Filter courses
			$filteredCourses =  array_filter($filteredCourses, function ($course) use ($formattedCodes) {
				return in_array(strtolower($course['ccvCode'] ?? ''), $formattedCodes);
			});
		}

		return $filteredCourses;
	}

}