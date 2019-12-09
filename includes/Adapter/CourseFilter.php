<?php
declare(strict_types=1);


namespace Dation\Woocommerce\Adapter;


class CourseFilter {
	/** @var array */
	private $courses;

	public function __construct(array $courses) {
		$this->courses = $courses;
	}

	public function filter_courses(?string $filters): array {
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
			return array_filter($this->courses, function ($course) use ($formattedCodes) {
				return in_array(strtolower($course['ccvCode'] ?? ''), $formattedCodes);
			});
		} else {
			return $this->courses;
		}
	}

}