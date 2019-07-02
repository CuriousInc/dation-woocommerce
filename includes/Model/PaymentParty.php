<?php
declare(strict_types=1);

namespace Dation\Woocommerce\Model;


class PaymentParty {
	public const STUDENT_TYPE = 'student';
	public const BANK_TYPE    = 'bank';

	/** @var string */
	private $type;

	/** @var int */
	private $id;

	public function getType():string {
		return $this->type;
	}

	public function getId():int {
		return $this->id;
	}

	public function setType(string $type):PaymentParty {
		$this->type = $type;
		return $this;
	}

	public function setId(int $id):PaymentParty {
		$this->id = $id;
		return $this;
	}

}