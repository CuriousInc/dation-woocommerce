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
	protected $emailAddress;

	/** @var string|null */
	protected $mobileNumber;

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

	/** @var string|null */
	protected $placeOfBirth;

	/** @var string|null */
	protected $identityCardNumber;

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

	public function getEmailAddress(): ?string {
		return $this->emailAddress;
	}

	public function setEmailAddress(?string $emailAddress): Student {
		$this->emailAddress = $emailAddress;

		return $this;
	}

	public function getMobileNumber(): ?string {
		return $this->mobileNumber;
	}

	public function setMobileNumber(?string $mobileNumber): Student {
		$this->mobileNumber = $mobileNumber;

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

	public function getPlaceOfBirth(): ?string {
		return $this->placeOfBirth;
	}

	public function setPlaceOfBirth(?string $placeOfBirth): Student {
		$this->placeOfBirth = $placeOfBirth;

		return $this;
	}

	public function getIdentityCardNumber(): ?string {
		return $this->identityCardNumber;
	}

	public function setIdentityCardNumber(?string $identityCardNumber): Student {
		$this->identityCardNumber = $identityCardNumber;

		return $this;
	}


}