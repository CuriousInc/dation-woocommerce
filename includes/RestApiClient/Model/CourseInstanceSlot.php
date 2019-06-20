<?php

namespace Dation\Woocommerce\RestApiClient\Model;


class CourseInstanceSlot {
	/** @var int $id */
	private $id;

	/** @var \DateTime $startDate */
	private $startDate;

	/** @var \DateTime $endDate */
	private $endDate;

	/** @var string $city */
	private $city;

	/** @var int $remainingAttendeeCapacity */
	private $remainingAttendeeCapacity;

	public function getId(): int {
		return $this->id;
	}

	public function getStartDate(): \DateTime {
		return $this->startDate;
	}

	public function getEndDate(): \DateTime {
		return $this->endDate;
	}

	public function getCity(): string {
		return $this->city;
	}

	public function getRemainingAttendeeCapacity(): int {
		return $this->remainingAttendeeCapacity;
	}

	public function setId(int $id): CourseInstanceSlot {
		$this->id = $id;
		return $this;
	}

	public function setStartDate(string $startDate): CourseInstanceSlot {
		$this->startDate = \DateTime::createFromFormat(\DateTimeInterface::ATOM, $startDate) ?: null;
		return $this;
	}

	public function setEndDate(string $endDate): CourseInstanceSlot {
		$this->endDate = \DateTime::createFromFormat(\DateTimeInterface::ATOM, $endDate) ?: null;

		return $this;
	}

	public function setCity(string $city): CourseInstanceSlot {
		$this->city = $city;
		return $this;
	}

	public function setRemainingAttendeeCapacity(int $remainingAttendeeCapacity): CourseInstanceSlot {
		$this->remainingAttendeeCapacity = $remainingAttendeeCapacity;
		return $this;
	}
}