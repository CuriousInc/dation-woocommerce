<?php
declare(strict_types=1);

const DW_DEFAULT_PRODUCT_PROPERTIES = [
	'virtual'           => true,
	'manage_stock'      => true,
	'sold_individually' => true,
	'low_stock_amount'  => 0,
];

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

	$productId = $wpdb->get_var($wpdb->prepare("SELECT post_id FROM $wpdb->postmeta WHERE meta_key='_sku' AND meta_value='%s' LIMIT 1", $sku));

	if($productId) {
		return new WC_Product($productId);
	}

	return null;
}

/**
 * @param mixed[] $course
 *
 * @return WC_Product
 *
 * @throws WC_Data_Exception
 * @throws \Exception When $course.startDate cannot be converted to DateTime
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

	$prettyDate = date_i18n('l d F Y', $startDate->getTimestamp()) . ' ' . $startDate->format('H:i');

	$product->set_name($course['name'] . ' ' . $prettyDate);
	$product->set_menu_order($startDate->getTimestamp());

	$product->set_description($course['name']);
	$product->set_short_description($course['ccvCode']);
	$product->set_sku($course['id']);
	$product->set_regular_price($dw_options['tkm_price']);
	$product->set_virtual(DW_DEFAULT_PRODUCT_PROPERTIES['virtual']);
	$product->set_manage_stock(DW_DEFAULT_PRODUCT_PROPERTIES['manage_stock']);
	$product->set_stock_quantity($dw_options['tkm_capacity']);
	$product->set_sold_individually(DW_DEFAULT_PRODUCT_PROPERTIES['sold_individually']);
	$product->set_low_stock_amount(DW_DEFAULT_PRODUCT_PROPERTIES['low_stock_amount']);
	$product->save();

	wp_set_object_terms($product->get_id(), $startDate->format('d-m-Y'), 'pa_datum', false);
	wp_set_object_terms($product->get_id(), $startDate->format('H:i'), 'pa_tijd', false);
	wp_set_object_terms($product->get_id(), $course['parts'][0]['slots'][0]['city'], 'pa_locatie', false);

	update_post_meta($product->get_id(), '_product_attributes', $attributes);

	return $product;
}
