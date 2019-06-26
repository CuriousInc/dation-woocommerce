<?php

declare(strict_types=1);

namespace Dation\Woocommerce\Model;


class Enrollment {
	/** @var Student $student */
	private $student;

	/** @var CourseInstanceSlot[] $slots */
	private $slots;

	/** @var array $company */
	private $company;

	public function getStudent(): Student {
		return $this->student;
	}

	public function getSlots(): array {
		return $this->slots;
	}

	public function getCompany(): ?array {
		return $this->company;
	}

	public function setStudent(Student $student): Enrollment {
		$this->student = $student;
		return $this;
	}

	public function setSlots(array $slots): Enrollment {
		$this->slots = $slots;
		return $this;
	}

	public function setCompany(?array $company): Enrollment {
		$this->company = $company;
		return $this;
	}
}