<?php

declare(strict_types=1);

use Dation\Woocommerce\Adapter\RestApiClientFactory;
use Dation\Woocommerce\Adapter\CourseFilter;
use Dation\Woocommerce\ProductService;

/**
 * @return WC_Product[]
 *
 * @throws WC_Data_Exception
 * @throws Exception
 */
function dw_import_products() {
	global $dw_options;

	$productService   = new ProductService($dw_options['default_course_price']);
	$client           = RestApiClientFactory::getClient();
	$courses          = $client->getCourseInstances(new DateTime(), null) ?? [];
	$finishedProducts = [];

	$courseFilter    = new CourseFilter($courses);
	$filteredCourses = $courseFilter->filter_courses_on_ccv_code_and_private($dw_options['ccv_code']);

	foreach($filteredCourses as $dationProduct) {
		$woocommerceProduct = dw_get_product_by_sku($dationProduct['id']);
		if($woocommerceProduct === null) {
			$product            = $productService->createOrUpdateWoocommerceProductFromDationCourse($dationProduct);
			$finishedProducts[] = $product;
		} else {
			$product            = $productService->createOrUpdateWoocommerceProductFromDationCourse($dationProduct, $woocommerceProduct);
			$finishedProducts[] = $product;
		}
		$startDate = $productService->getStartDateFromData($dationProduct);
		$prettyDate = date_i18n(PRETTY_DATE, $startDate->getTimestamp()) . ' ' . $startDate->format(DUTCH_TIME);

		if(isset($dw_options['use_tkm'])) {
			$product = $productService->setName($product, $dationProduct['name'] . ' ' . $prettyDate);
		}

		dw_set_product_terms(
			$product,
			$dationProduct,
			$startDate,
			$productService->formatAddress($dationProduct['parts'][0]['slots'][0]['location']['address'])
		);

		$contactFormLocation = $dw_options['contact-form'] ?? 'contactformulier';
		$productService->setExternalUrlForProduct($product, $dationProduct, $contactFormLocation, get_site_url());
	}

	dw_delete_products($filteredCourses);

	return $finishedProducts;
}

/**
 * @param WC_Product $woocommerceProduct
 * @param array $course
 * @param $startDate
 * @param $addressLine
 *
 * @throws Exception
 */
function dw_set_product_terms(WC_Product $woocommerceProduct, array $course, $startDate, $addressLine): void {
	// set terms after saving products
	$city = $course['parts'][0]['slots'][0]['city'];
	wp_set_object_terms($woocommerceProduct->get_id(), $city, 'pa_locatie', false);
	wp_set_object_terms($woocommerceProduct->get_id(), $course['ccvCode'], 'pa_ccv_code', false);

	dw_format_and_save_dates($woocommerceProduct, $startDate, $course['parts']);
	wp_set_object_terms($woocommerceProduct->get_id(), $addressLine, 'pa_address', false);

	$attributes = ProductService::getProductAttributes($startDate, $city);
	update_post_meta($woocommerceProduct->get_id(), '_product_attributes', $attributes);
}

/**
 * Move product in the passed to the trash bin. Called when importing
 */
function dw_delete_products(array $filteredCourses) {
	global $dw_options;

	$products = wc_get_products([
		'limit' => -1
	]);

	$productsToRemove = array_filter(
		$products,
		function (WC_Product $product) use ($filteredCourses, $dw_options) {
			if(false === array_search($product->get_sku(), array_column($filteredCourses, 'id'))) {
				return true;
			}
			if(isset($dw_options['remove_courses']) && $product->get_stock_quantity() < 1) {
				return true;
			}

			return false;
		}
	);

	foreach($productsToRemove as $product) {
		$product->delete();
	}

	$products = wc_get_products([
		'limit' => -1
	]);

	$currentTimestamp = (new DateTime())
		->setTime(23, 59, 59)
		->getTimestamp();

	array_walk($products, function (WC_Product $product) use ($currentTimestamp) {
		if((int)$product->get_menu_order() <= $currentTimestamp) {
			$product->delete();
		}
	});
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
 * @param WC_Product $product
 * @param DateTime $date
 * @param array $courseParts
 *
 * @throws Exception
 */
function dw_format_and_save_dates(WC_Product $product, DateTime $date, array $courseParts) {
	wp_set_object_terms($product->get_id(), $date->format('d-m-Y'), 'pa_datum', false);
	wp_set_object_terms($product->get_id(), $date->format('H:i'), 'pa_tijd', false);
	wp_set_object_terms($product->get_id(), date_i18n('l d F', $date->getTimestamp()), 'pa_pretty_date', false);
	wp_set_object_terms($product->get_id(), date_i18n('F', $date->getTimestamp()), 'pa_month', false);

	// remove old slot values
	wp_delete_object_term_relationships($product->get_id(), 'pa_slot_time');

	foreach($courseParts as $part) {
		$startDate      = new DateTime($part['slots'][0]['startDate']);
		$endDate        = new DateTime($part['slots'][0]['endDate']);
		$attributeValue = date_i18n('D d F Y', $startDate->getTimestamp()) . ' ' . $startDate->format('H:i');
		$attributeValue .= '-' . $endDate->format('H:i');

		wp_set_object_terms(
			$product->get_id(),
			$attributeValue,
			'pa_slot_time',
			true
		);
	}
}
