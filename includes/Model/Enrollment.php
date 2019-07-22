<?php

declare(strict_types=1);

namespace Dation\Woocommerce\Model;


class Enrollment {
	/** @var Student $student */
	private $student;

	/** @var CourseInstanceSlot[] $slots */
	private $slots;

	/** @var int */
	private $id;

	public function getStudent(): Student {
		return $this->student;
	}

	public function getSlots(): ?array {   
		return $this->slots;
	}

	public function setStudent(Student $student): Enrollment {
		$this->student = $student;
		return $this;
	}

	public function setSlots(array $slots): Enrollment {
		$this->slots = $slots;
		return $this;
	}

	public function getId(): ?int {
		return $this->id;
	}

	public function setId(?int $id): Enrollment {
		$this->id = $id;
		return $this;
	}
}