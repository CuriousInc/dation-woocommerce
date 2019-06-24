<?php

declare(strict_types=1);

namespace Dation\Woocommerce\RestApiClient\Model;


class CourseInstancePart {

	/** @var string $name */
	private $name;

	/** @var CourseInstanceSlot[] $slots */
	private $slots;

	/**
	 * @return string
	 */
	public function getName(): string {
		return $this->name;
	}

	public function getSlots(): ?array {
		return $this->slots;
	}

	public function setName(string $name): CourseInstancePart {
		$this->name = $name;
		return $this;
	}

	public function setSlots(array $slots): CourseInstancePart {
		$this->slots = $slots;
		return $this;
	}


}