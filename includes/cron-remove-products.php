<?php

declare(strict_types=1);

/**
 * Register wp-cron events for fetching Dation products and transforming them to Woocommerce products
 */

require 'import-products.php';

const DW_DELETE_PRODUCTS_HOOK = 'dw_delete_dation_products';

add_action(DW_DELETE_PRODUCTS_HOOK, 'dw_delete_products');

if(!wp_next_scheduled(DW_DELETE_PRODUCTS_HOOK)) {
	wp_schedule_event(time(), 'daily', DW_DELETE_PRODUCTS_HOOK);
}

register_deactivation_hook(DW_PLUGIN_FILE, 'dw_deactivate_cron');

function dw_deactivate_cron() {
	$timestamp = wp_next_scheduled(DW_DELETE_PRODUCTS_HOOK);
	wp_unschedule_event($timestamp, DW_DELETE_PRODUCTS_HOOK);
}
