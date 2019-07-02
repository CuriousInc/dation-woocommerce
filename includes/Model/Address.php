<?php

declare(strict_types=1);

namespace Dation\Woocommerce\Model;

class Address {

	/** @var string|null */
	protected $streetName;

	/** @var string|null */
	protected $houseNumber;

	/** @var string|null */
	protected $postalCode;

	/** @var string|null */
	protected $city;

	public function setStreetName(?string $streetName): Address {
		$this->streetName = $streetName;
		return $this;
	}

	public function getStreetName(): ?string {
		return $this->streetName;
	}

	public function getHouseNumber(): ?string {
		return $this->houseNumber;
	}

	public function setHouseNumber(?string $houseNumber): Address {
		$this->houseNumber = $houseNumber;
		return $this;
	}

	public function getPostalCode(): ?string {
		return $this->postalCode;
	}

	public function setPostalCode(?string $postalCode): Address {
		$this->postalCode = $postalCode;
		return $this;
	}

	public function getCity(): ?string {
		return $this->city;
	}

	public function setCity(?string $city): Address {
		$this->city = $city;
		return $this;
	}
}