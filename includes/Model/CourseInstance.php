<?php

declare(strict_types=1);

namespace Dation\Woocommerce\Model;


class CourseInstance {
	/** @var int $id */
	private $id;

	/** @var string $name */
	private $name;

	/** @var int $code95PracticeHours */
	private $code95PracticeHours;

	/** @var int $code95TheoryHours */
	private $code95TheoryHours;

	/** @var \DateTime $startDate */
	private $startDate;

	/** @var int $remainingAttendeeCapacity */
	private $remainingAttendeeCapacity;

	/** @var string|null $ccvCode  */
	private $ccvCode;

	/** @var CourseInstancePart[] $parts*/
	private $parts;

	public function getId(): int {
		return $this->id;
	}

	public function setId(int $id): CourseInstance {
		$this->id = $id;
		return $this;
	}

	public function getName(): string {
		return $this->name;
	}

	public function setName(string $name): CourseInstance {
		$this->name = $name;
		return $this;
	}

	public function getCode95PracticeHours(): int {
		return $this->code95PracticeHours;
	}

	public function setCode95PracticeHours(int $code95PracticeHours): CourseInstance {
		$this->code95PracticeHours = $code95PracticeHours;
		return $this;
	}

	public function getCode95TheoryHours(): int {
		return $this->code95TheoryHours;
	}

	public function setCode95TheoryHours(int $code95TheoryHours): CourseInstance {
		$this->code95TheoryHours = $code95TheoryHours;
		return $this;
	}

	public function getStartDate(): ?\DateTime {
		return $this->startDate;
	}

	public function setStartDate(?\DateTime $startDate): CourseInstance {
		$this->startDate = $startDate;
		return $this;
	}

	public function getRemainingAttendeeCapacity(): int {
		return $this->remainingAttendeeCapacity;
	}

	public function setRemainingAttendeeCapacity(int $remainingAttendeeCapacity): CourseInstance {
		$this->remainingAttendeeCapacity = $remainingAttendeeCapacity;
		return $this;
	}

	public function getCcvCode(): ?string {
		return $this->ccvCode;
	}

	public function setCcvCode(?string $ccvCode): CourseInstance {
		$this->ccvCode = $ccvCode;
		return $this;
	}

	public function getParts(): array {
		return $this->parts;
	}

	public function setParts(array $parts): CourseInstance {
		$this->parts = $parts;
		return $this;
	}
}