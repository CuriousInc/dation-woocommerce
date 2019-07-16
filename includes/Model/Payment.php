<?php
declare(strict_types=1);

namespace Dation\Woocommerce\Model;

class Payment {

	/** @var PaymentParty|null */
	private $payer;

	/** @var PaymentParty|null */
	private $payee;

	/** @var float|null */
	private $amount;

	/** @var string|null */
	private $description;

	/** @var Invoice */
	private $invoice;

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

	public function getInvoice(): Invoice {
		return $this->invoice;
	}

	public function setInvoice(Invoice $invoice): Payment {
		$this->invoice = $invoice;
		return $this;
	}

}