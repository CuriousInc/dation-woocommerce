<?php
declare(strict_types=1);

namespace Dation\Woocommerce\Model;

class Payment {

	/** @var int|null $id */
	private $id;

	/** @var PaymentParty|null */
	private $payer;

	/** @var PaymentParty|null */
	private $payee;

	/** @var float|null */
	private $amount;

	/** @var string|null */
	private $description;

	public function getId(): ?int {
		return $this->id;
	}

	public function getPayer(): ?PaymentParty {
		return $this->payer;
	}

	public function getPayee(): ?PaymentParty {
		return $this->payee;
	}

	public function getAmount(): ?float {
		return $this->amount;
	}

	public function getDescription(): ?string {
		return $this->description;
	}

	public function setId(?int $id): Payment {
		$this->id = $id;
		return $this;
	}

	public function setPayer(?PaymentParty $payer): Payment {
		$this->payer = $payer;
		return $this;
	}

	public function setPayee(?PaymentParty $payee): Payment {
		$this->payee = $payee;
		return $this;
	}

	public function setAmount(?float $amount): Payment {
		$this->amount = $amount;
		return $this;
	}

	public function setDescription(?string $description): Payment {
		$this->description = $description;
		return $this;
	}

}