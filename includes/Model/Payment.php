<?php
declare(strict_types=1);

namespace Dation\Woocommerce\Model;


class Payment {
	/** @var PaymentParty */
	private $payer;

	/** @var PaymentParty */
	private $payee;

	/** @var float */
	private $amount;

	/** @var string */
	private $description;

	/** @var string */
	private $gatewayTransactionId;

	public function getPayer(): PaymentParty {
		return $this->payer;
	}

	public function getPayee(): PaymentParty {
		return $this->payee;
	}

	public function getAmount(): float {
		return $this->amount;
	}


	public function getDescription(): ?string {
		return $this->description;
	}

	public function getGatewayTransactionId(): ?string {
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

	public function setAmount(float $amount): Payment {
		$this->amount = $amount;
		return $this;
	}

	public function setDescription(string $description): Payment {
		$this->description = $description;
		return $this;
	}

	public function setGatewayTransactionId(string $gatewayTransactionId): Payment {
		$this->gatewayTransactionId = $gatewayTransactionId;
		return $this;
	}

}