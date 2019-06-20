<?php

namespace Dation\Woocommerce\RestApiClient\Model;


class CourseInstancePart {

	/** @var string $name */
	private $name;

	/** @var CourseInstanceSlot[] $courseInstanceSlots */
	private $courseInstanceSlots;

	/**
	 * @return string
	 */
	public function getName(): string {
		return $this->name;
	}

	public function getCourseInstanceSlots(): array {
		return $this->courseInstanceSlots;
	}

	public function setName(string $name): CourseInstancePart {
		$this->name = $name;
		return $this;
	}

	public function setCourseInstanceSlots(array $courseInstanceSlots): CourseInstancePart {
		$this->courseInstanceSlots = $courseInstanceSlots;
		return $this;
	}


}