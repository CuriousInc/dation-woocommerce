<?php

declare(strict_types=1);

namespace Dation\Woocommerce\Model;

use DateTime;

class Student {

	/** @var int|null */
	protected $id;

	/** @var string|null */
	protected $firstName;

	/** @var string|null */
	protected $lastName;

	/** @var Address|null */
	protected $residentialAddress;

	/** @var string|null */
	protected $email;

	/** @var string|null */
	protected $phone;

	/** @var \DateTime|null */
	protected $dateOfBirth;

	/** @var \DateTime|null */
	protected $issueDateCategoryBDrivingLicense;

	/** @var string|null */
	protected $nationalRegistryNumber;

	/** @var bool */
	protected $planAsIndependent = false;

	/** @var string|null */
	protected $comments;

	public function getId(): ?int {
		return $this->id;
	}

	public function setId(?int $id): Student {
		$this->id = $id;

		return $this;
	}

	public function getFirstName(): ?string {
		return $this->firstName;
	}

	public function setFirstName(string $get_billing_first_name): Student {
		$this->firstName = $get_billing_first_name;

		return $this;
	}

	public function getLastName(): ?string {
		return $this->lastName;
	}

	public function setLastName(?string $lastName): Student {
		$this->lastName = $lastName;

		return $this;
	}

	public function getEmail(): ?string {
		return $this->email;
	}

	public function setEmail(?string $email): Student {
		$this->email = $email;

		return $this;
	}

	public function getPhone(): ?string {
		return $this->phone;
	}

	public function setPhone(?string $phone): Student {
		$this->phone = $phone;

		return $this;
	}

	public function getComments(): ?string {
		return $this->comments;
	}

	public function setComments(?string $comments): Student {
		$this->comments = $comments;

		return $this;
	}

	public function isPlanAsIndependent(): bool {
		return $this->planAsIndependent;
	}

	public function setPlanAsIndependent(bool $planAsIndependent): Student {
		$this->planAsIndependent = $planAsIndependent;

		return $this;
	}

	public function getNationalRegistryNumber(): ?string {
		return $this->nationalRegistryNumber;
	}

	public function setNationalRegistryNumber(?string $nationalRegistryNumber): Student {
		$this->nationalRegistryNumber = $nationalRegistryNumber;

		return $this;
	}

	public function getIssueDateCategoryBDrivingLicense(): ?DateTime {
		return $this->issueDateCategoryBDrivingLicense;
	}

	public function setIssueDateCategoryBDrivingLicense(?DateTime $issueDate): Student {
		$this->issueDateCategoryBDrivingLicense = $issueDate;

		return $this;
	}

	public function getDateOfBirth(): ?DateTime {
		return $this->dateOfBirth;
	}

	public function setDateOfBirth(?DateTime $dateOfBirth): Student {
		$this->dateOfBirth = $dateOfBirth;

		return $this;
	}

	public function getResidentialAddress(): ?Address {
		return $this->residentialAddress;
	}

	public function setResidentialAddress(?Address $residentialAddress): Student {
		$this->residentialAddress = $residentialAddress;

		return $this;
	}
}