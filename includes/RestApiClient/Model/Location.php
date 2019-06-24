<?php

declare(strict_types=1);

namespace Dation\Woocommerce\RestApiClient\Model;


class Location {
	/** @var string $name */
	private $name;

	/** @var int $maxOccupancy */
	private $maxOccupancy;

	/** @var string $type */
	private $type;

	/** @var int $distance */
	private $distance;

	/** @var Address $address */
	private $address;

	public function getName(): string {
		return $this->name;
	}

	public function getMaxOccupancy(): int {
		return $this->maxOccupancy;
	}

	public function getType(): string {
		return $this->type;
	}

	public function getDistance(): int {
		return $this->distance;
	}

	public function getAddress(): Address {
		return $this->address;
	}

	public function setName(string $name): Location {
		$this->name = $name;
		return $this;
	}

	public function setMaxOccupancy(int $maxOccupancy): Location {
		$this->maxOccupancy = $maxOccupancy;
		return $this;
	}

	public function setType(string $type): Location {
		$this->type = $type;
		return $this;
	}

	public function setDistance(int $distance): Location {
		$this->distance = $distance;
		return $this;
	}

	public function setAddress(Address $address): Location {
		$this->address = $address;
		return $this;
	}
}