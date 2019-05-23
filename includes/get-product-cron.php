<?php
/**
 * Register wp-cron events for fetching Dation products and transforming them to Woocommerce products
 */

const HOOK_NAME = 'dw_import_dation_products';

const VARIABLES    = [
	'virtual'           => true,
	'manage_stock'      => true,
	'sold_individually' => true,
	'low_stock_amount'  => 0,
];

add_action(HOOK_NAME, 'dw_import_products');

if(!wp_next_scheduled(HOOK_NAME)) {
	wp_schedule_event(time(), 'hourly', HOOK_NAME);
}

register_deactivation_hook(__FILE__, 'deactivate_cron');

function deactivate_cron(){
	$timestamp = wp_next_scheduled(HOOK_NAME);
	wp_unschedule_event($timestamp, HOOK_NAME);
}

/**
 * @return WC_Product[]
 *
 * @throws WC_Data_Exception
 */
function dw_import_products() {
	global $dw_options;

	$client = new Dation\Woocommerce\RestApiClient\RestApiClient($dw_options['api_key'], $dw_options['handle']);
	$courses = $client->getCourseInstances(new DateTime(), null) ?? [];

	$createdProducts = [];

	foreach($courses as $dationProduct) {
		if(dw_get_product_by_sku($dationProduct['id']) === null) {
			$product           = dw_add_woocommerce_product($dationProduct);
			$createdProducts[] = $product;
		}
	}

	return $createdProducts;
}

/**
 * Get product by unique `stock keep unit`.
 * When creating a product, dation course ID is set to the sku of a woocommerce product.
 * When synchronizing with Dation, we can use this to find Dation courses that are not in Woocommerce
 *
 * @param $sku
 *
 * @return null|WC_Product
 */
function dw_get_product_by_sku($sku) {
	global $wpdb;

	$product_id = $wpdb->get_var($wpdb->prepare("SELECT post_id FROM $wpdb->postmeta WHERE meta_key='_sku' AND meta_value='%s' LIMIT 1", $sku));

	if($product_id) {
		return new WC_Product($product_id);
	}

	return null;
}

/**
 * @param mixed[] $course
 *
 * @return WC_Product
 *
 * @throws WC_Data_Exception
 */
function dw_add_woocommerce_product($course) {
	global $dw_options;
	$startDate = new DateTime($course['startDate']);

	$attributes = [
		'pa_datum'   => [
			'name'        => 'pa_datum',
			'value'       => $startDate->format('d-m-Y'),
			'position'    => 1,
			'is_visible'  => true,
			'is_taxonomy' => '1'
		],
		'pa_locatie' => [
			'name'        => 'pa_locatie',
			'value'       => $course['parts'][0]['slots'][0]['city'],
			'is_visible'  => true,
			'is_taxonomy' => '1'
		],
		'pa_tijd'    => [
			'name'        => 'pa_tijd',
			'value'       => $startDate->format('H:i'),
			'is_visible'  => true,
			'is_taxonomy' => '1'
		]
	];
	$product    = new WC_Product();

	$timestamp  = $startDate->getTimestamp();
	$prettyDate = date_i18n('l d F Y H:i', $timestamp);

	$product->set_name($course['name'] . ' ' . $prettyDate);
	$product->set_menu_order($timestamp);

	$product->set_description($course['name']);
	$product->set_short_description($course['ccvCode']);
	$product->set_sku($course['id']);
	$product->set_regular_price($dw_options['tkm_price']);
	$product->set_virtual(VARIABLES['virtual']);
	$product->set_manage_stock(VARIABLES['manage_stock']);
	$product->set_stock_quantity($dw_options['tkm_capacity']);
	$product->set_sold_individually(VARIABLES['sold_individually']);
	$product->set_low_stock_amount(VARIABLES['low_stock_amount']);
	$product->save();

	wp_set_object_terms($product->get_id(), $startDate->format('d-m-Y'), 'pa_datum', false);
	wp_set_object_terms($product->get_id(), $startDate->format('H:i'), 'pa_tijd', false);
	wp_set_object_terms($product->get_id(), $course['parts'][0]['slots'][0]['city'], 'pa_locatie', false);

	update_post_meta($product->get_id(), '_product_attributes', $attributes);

	return $product;
}