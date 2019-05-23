<?php
declare(strict_types=1);

const VARIABLES = [
	'virtual' =>           true,
	'manage_stock' =>      true,
	'sold_individually' => true,
	'low_stock_amount' =>  0,
];
const BASE_API_URL = 'https://dashboard.dation.nl/api/v1/';


date_default_timezone_set('Europe/Amsterdam');

// WP_List_Table is not loaded automatically so we need to load it in our application
if(!class_exists('WP_List_Table')) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

if(!class_exists('DationProductList')) {
	require_once ABSPATH . 'wp-content/plugins/dation-woocommerce/admin/DationProductList.php';
}

function dw_notice_error(string $msg): string {
	return '<div class="notice notice-error"><p>' . $msg . '</p></div>';
}

function dw_notice_info(string $msg): string {
	return '<div class="notice notice-info"><p>' . $msg . '</p></div>';
}

function dw_show_course_page() {
	$newProductsCount = 0;
	try {
		$newProductsCount = dw_get_products();
	} catch (Throwable $e) {
		dw_notice_error('Er is iets misgegaan bij het opslaan van het product. Herlaad de pagina en probeer het opnieuw.');
	}

	if($newProductsCount > 0 ) {
		echo dw_notice_info('Er zijn ' . $newProductsCount . ' cursussen gesynchroniseerd met Dation');
	}

	$table = new DationProductList();
	$table->prepare_items();

	?>
	<h1>Cursussen</h1>
	<div class="wrap">
		<div id="icon-users" class="icon32"></div>
		<?php $table->display(); ?>
	</div>
	<?php
}

/**
 * @return int
 *
 * @throws WC_Data_Exception
 */
function dw_get_products() {
	$courses = dw_get_course_instances(new DateTime(), null) ?? [];

	$createdProducts = [];

	foreach($courses as $dationProduct) {
		if(dw_get_product_by_sku($dationProduct['id']) === null) {
			$product = dw_add_woocommerce_product($dationProduct);
			$createdProducts[] = $product;
		}
	}

	return count($createdProducts);
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
		'pa_datum' => [
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
		'pa_tijd' => [
			'name'        => 'pa_tijd',
			'value'       => $startDate->format('H:i'),
			'is_visible'  => true,
			'is_taxonomy' => '1'
		]
	];
	$product = new WC_Product();

	$timestamp = $startDate->getTimestamp();
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

	$product_id = $wpdb->get_var( $wpdb->prepare("SELECT post_id FROM $wpdb->postmeta WHERE meta_key='_sku' AND meta_value='%s' LIMIT 1", $sku));

	if($product_id) {
		return new WC_Product($product_id);
	}

	return null;
}

/**
 * @param DateTime|null $startDateAfter
 * @param DateTime|null $startDateBefore
 *
 * @return array|null|object
 */
function dw_get_course_instances(DateTime $startDateAfter = null, DateTime $startDateBefore = null) {
	global $dw_options;

	$beforeParam = $startDateBefore ? '&startDateBefore=' . $startDateBefore->format('Y-m-d') : '';
	$afterParam = $startDateAfter ? '&startDateAfter=' . $startDateAfter->format('Y-m-d') : '';

	$curl = curl_init();

	curl_setopt_array($curl, array(
		CURLOPT_URL => BASE_API_URL . 'course-instances?' . $beforeParam . $afterParam,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => '',
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 30,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => 'GET',
		CURLOPT_HTTPHEADER => array(
			'Accept: */*',
			'Authorization: Basic ' . $dw_options['api_key'],
			'Cache-Control: no-cache',
			'Connection: keep-alive',
			'accept-encoding: gzip, deflate',
			'handle: dw-' . $dw_options['handle'],
		),
	));

	$response = curl_exec($curl);
	$err = curl_error($curl);

	curl_close($curl);

	if ($err) {
		echo dw_notice_error('Er is iets misgegaan bij het synchroniseren van de producten. Herlaad de pagina en probeer het opnieuw');
	} else {
		return json_decode($response, true);
	}
	return null;
}
