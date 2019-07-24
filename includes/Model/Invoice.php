<?php

declare(strict_types=1);

namespace Dation\Woocommerce\Model;

class Invoice {
	/** @var int */
	private $id;

	public function getId(): ?int {
		return $this->id;
	}

	public function setId(?int $id): Invoice {
		$this->id = $id;
		return $this;
	}

}