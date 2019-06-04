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
	 * Process Order
	 *
	 * This function is called when an order is set to status "Processing".
	 * This means payment has been received (paid) and stock reduced; order is
	 * awaiting fulfillment.
	 *
	 * In our context, fulfillment means synchronizing its changes to Dation
	 *
	 * @param \WC_Order $order
	 */
	public function procesOrder(\WC_Order $order) {
		throw new \BadMethodCallException(sprintf('Function %s is not implemented yet', __FUNCTION__));
	}
}