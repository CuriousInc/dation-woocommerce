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
		'pa_pretty_date' => [
			'name'        => 'pa_pretty_date',
			'is_visible'  => true,
			'is_taxonomy' => true,
		],
		'pa_address' => [
			'name' => 'pa_address',
			'is_visible' => true,
			'is_taxonomy' => true,
		],
	];
	$product    = new WC_Product();

	$prettyDate = date_i18n(PRETTY_DATE, $startDate->getTimestamp()) . ' ' . $startDate->format(DUTCH_TIME);

	if(isset($dw_options['use_tkm'])) {
		$product->set_name($course['name'] . ' ' . $prettyDate);
	} else {
		$product->set_name($course['name']);
	}
	$product->set_menu_order($startDate->getTimestamp());

	$product->set_description($course['name']);
	$product->set_short_description($course['ccv_code'] ?? '');
	$product->set_sku($course['id']);
	$product->set_regular_price($dw_options['default_course_price']);
	$product->set_virtual(DW_DEFAULT_PRODUCT_PROPERTIES['virtual']);
	$product->set_manage_stock(DW_DEFAULT_PRODUCT_PROPERTIES['manage_stock']);
	$product->set_stock_quantity($course['remainingAttendeeCapacity']);
	$product->set_sold_individually(DW_DEFAULT_PRODUCT_PROPERTIES['sold_individually']);
	$product->set_low_stock_amount(DW_DEFAULT_PRODUCT_PROPERTIES['low_stock_amount']);
	$product->save();

	wp_set_object_terms($product->get_id(), $course['parts'][0]['slots'][0]['city'], 'pa_locatie', false);

	$courseParts = $course['parts'];
	usort($courseParts, function ($a, $b) {
		$startA = (DateTime::createFromFormat(DATE_ISO8601, $a['slots'][0]['startDate']))->getTimestamp();
		$startB = (DateTime::createFromFormat(DATE_ISO8601, $b['slots'][0]['startDate']))->getTimestamp();
		if($startA === $startB) {
			return 0;
		}
		return ($startA < $startB) ? -1 : 1;

	});

	dw_format_and_save_dates($product, $startDate, $courseParts);
	dw_format_and_save_address($product, $course['parts'][0]['slots'][0]['location']['address']);

	update_post_meta($product->get_id(), '_product_attributes', $attributes);

	return $product;
}

/**
 * @param WC_Product $product
 * @param DateTime $date
 * @param array $courseParts
 *
 * @throws Exception
 */
function dw_format_and_save_dates(WC_Product $product, DateTime $date, array $courseParts) {
	wp_set_object_terms($product->get_id(), $date->format('d-m-Y'), 'pa_datum', false);
	wp_set_object_terms($product->get_id(), $date->format('H:i'), 'pa_tijd', false);
	wp_set_object_terms($product->get_id(), date_i18n('l d F Y', $date->getTimestamp()), 'pa_pretty_date', false);

	$i = 1;
	foreach($courseParts as $part) {
		$startDate      = new DateTime($part['slots'][0]['startDate']);
		$endDate        = new DateTime($part['slots'][0]['endDate']);
		$attributeValue = $i . '. ' . date_i18n('D d F Y', $startDate->getTimestamp()) . ' ' . $startDate->format('H:i');
		$attributeValue .= '-' . $endDate->format('H:i');

		wp_set_object_terms(
			$product->get_id(),
			$attributeValue,
			'pa_slot_time',
			true
		);
		$i++;
	}
}

function dw_format_and_save_address(WC_Product $product, array $address) {
	$streetLine = implode(', ', array_filter([
		$address['streetName'], $address['houseNumber'], $address['addition']
	]));

	$addressLine = implode(', ', array_filter([
		$streetLine, $address['postalCode'], $address['city']
	]));

	wp_set_object_terms($product->get_id(), $addressLine, 'pa_address', false);
}