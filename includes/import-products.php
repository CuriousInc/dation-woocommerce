<?php

declare(strict_types=1);

use Dation\Woocommerce\Adapter\RestApiClientFactory;
use Dation\Woocommerce\Adapter\CourseFilter;

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
 * @throws Exception
 */
function dw_import_products() {
	global $dw_options;

	$client          = RestApiClientFactory::getClient();
	$courses         = $client->getCourseInstances(new DateTime(), null) ?? [];
	$createdProducts = [];

	$courseFilter = new CourseFilter($courses);
	$filteredCourses = $courseFilter->filter_courses($dw_options['ccv_code']);

	foreach($filteredCourses as $dationProduct) {
		if(dw_get_product_by_sku($dationProduct['id']) === null) {
			$product           = dw_add_woocommerce_product($dationProduct);
			$createdProducts[] = $product;
		}
	}

	return $createdProducts;
}

/**
 * Get product by unique `stock keep unit`.
 * When creating a product, Dation course ID is set to the sku of a woocommerce product.
 * When synchronizing with Dation, we can use this to find Dation courses that are not in Woocommerce
 *
 * @param $sku
 *
 * @return null|WC_Product
 */
function dw_get_product_by_sku($sku) {
	global $wpdb;

	$productId = $wpdb->get_var(
		$wpdb->prepare(
			"SELECT post_id FROM $wpdb->postmeta WHERE meta_key='_sku' AND meta_value='%s' LIMIT 1",
			$sku
		)
	);

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
		'pa_datum'     => [
			'name'        => 'pa_datum',
			'value'       => $startDate->format('d-m-Y'),
			'position'    => 1,
			'is_visible'  => true,
			'is_taxonomy' => true,
		],
		'pa_locatie'   => [
			'name'        => 'pa_locatie',
			'value'       => $course['parts'][0]['slots'][0]['city'],
			'is_visible'  => true,
			'is_taxonomy' => true,
		],
		'pa_tijd'      => [
			'name'        => 'pa_tijd',
			'value'       => $startDate->format('H:i'),
			'is_visible'  => true,
			'is_taxonomy' => true,
		],
		'pa_slot_time' => [
			'name'        => 'pa_slot_time',
			'is_visible'  => true,
			'is_taxonomy' => true,
		],
	];
	$product    = new WC_Product();

	$prettyDate = date_i18n('l d F Y', $startDate->getTimestamp()) . ' ' . $startDate->format('H:i');

	if(isset($dw_options['use_tkm'])) {
		$product->set_name($course['name'] . ' ' . $prettyDate);
	} else {
		$product->set_name($course['name']);
	}
	$product->set_menu_order($startDate->getTimestamp());

	$product->set_description($course['name']);
	$product->set_short_description($course['ccv_code'] ?? '');
	$product->set_sku($course['id']);
	$product->set_regular_price($dw_options['tkm_price']);
	$product->set_virtual(DW_DEFAULT_PRODUCT_PROPERTIES['virtual']);
	$product->set_manage_stock(DW_DEFAULT_PRODUCT_PROPERTIES['manage_stock']);
	$product->set_stock_quantity($course['remainingAttendeeCapacity']);
	$product->set_sold_individually(DW_DEFAULT_PRODUCT_PROPERTIES['sold_individually']);
	$product->set_low_stock_amount(DW_DEFAULT_PRODUCT_PROPERTIES['low_stock_amount']);
	$product->save();

	wp_set_object_terms($product->get_id(), $startDate->format('d-m-Y'), 'pa_datum', false);
	wp_set_object_terms($product->get_id(), $startDate->format('H:i'), 'pa_tijd', false);
	wp_set_object_terms($product->get_id(), $course['parts'][0]['slots'][0]['city'], 'pa_locatie', false);

	foreach($course['parts'] as $part) {
		$startDate = new DateTime($part['slots'][0]['startDate']);
		wp_set_object_terms(
			$product->get_id(),
			date_i18n('d F Y', $startDate->getTimestamp()) . ' ' . $startDate->format('H:i'),
			'pa_slot_time',
			true
		);
	}

	update_post_meta($product->get_id(), '_product_attributes', $attributes);

	return $product;
}