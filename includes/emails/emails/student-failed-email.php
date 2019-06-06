<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/** @var WC_Order $order */
?>

<p>Het is niet gelukt om de leerling te synchroniseren met Dation.</p>
<p><?php printf(__('Ga naar de orderdetails voor meer informatie: %d', 'woocommerce'), $order->get_edit_order_url() ); ?></p>

