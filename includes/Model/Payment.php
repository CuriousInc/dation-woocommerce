<?php

namespace Dation\Woocommerce\Model;


class Payment {
	/** @var PaymentParty */
	private $payer;

	/** @var PaymentParty */
	private $payee;

	/** @var int */
	private $amount;

	/** @var \DateTime */
	private $date;

	/** @var Invoice */
	private $invoice;

	/** @var string */
	private $description;

	/** @var Administration */
	private $administration;

	/** @var string */
	private $gatewayTransactionId;

	public function getPayer(): PaymentParty {
		return $this->payer;
	}

	public function getPayee(): PaymentParty {
		return $this->payee;
	}

	public function getAmount(): int {
		return $this->amount;
	}

	public function getDate(): \DateTime {
		return $this->date;
	}

	public function getInvoice(): Invoice {
		return $this->invoice;
	}

	public function getDescription(): string {
		return $this->description;
	}

	public function getAdministration(): Administration {
		return $this->administration;
	}

	public function getGatewayTransactionId(): string {
		return $this->gatewayTransactionId;
	}

	public function setPayer(PaymentParty $payer): Payment {
		$this->payer = $payer;
		return $this;
	}

	public function setPayee(PaymentParty $payee): Payment {
		$this->payee = $payee;
		return $this;
	}

	public function setAmount(int $amount): Payment {
		$this->amount = $amount;
		return $this;
	}

	public function setDate(\DateTime $date): Payment {
		$this->date = $date;
		return $this;
	}

	public function setInvoice(Invoice $invoice): Payment {
		$this->invoice = $invoice;
		return $this;
	}

	public function setDescription(string $description): Payment {
		$this->description = $description;
		return $this;
	}

	public function setAdministration(Administration $administration): Payment {
		$this->administration = $administration;
		return $this;
	}

	public function setGatewayTransactionId(string $gatewayTransactionId): Payment {
		$this->gatewayTransactionId = $gatewayTransactionId;
		return $this;
	}

}


class Administration {
	/** @var int */
	private $id;

	public function getId(): int {
		return $this->id;
	}

	public function setId(int $id): Administration {
		$this->id = $id;
		return $this;
	}
}

class Invoice {
	/** @var int */
	private $id;

	public function getId(): int {
		return $this->id;
	}

	public function setId(int $id): Invoice {
		$this->id = $id;
		return $this;
	}
}