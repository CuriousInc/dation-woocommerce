<?php

declare(strict_types=1);

namespace Dation\Woocommerce\Adapter;

/**
 * The OrderManager is a service responsible synchronizing Woocommerce orders with Dation.
 *
 * The OrderManager ingests changes to Woocommerce orders, translating them
 * to Dation resources and synchronizing them with Dation Dashboard.
 */
class OrderManager {

	/**
	 * Handle Order Change
	 *
	 * This function is called when an order is changed and its changes should be synchronized to Dation
	 *
	 * @param \WC_Order $order
	 */
	public function handleOrderChange(\WC_Order $order) {
		throw new \BadMethodCallException(sprintf('Function %s is not implemented yet', __FUNCTION__));
	}
}