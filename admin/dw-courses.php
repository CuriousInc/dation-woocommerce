<?php
declare(strict_types=1);

use Dation\Woocommerce\Admin\DationProductList;

const VARIABLES    = [
	'virtual'           => true,
	'manage_stock'      => true,
	'sold_individually' => true,
	'low_stock_amount'  => 0,
];

date_default_timezone_set('Europe/Amsterdam');

// WP_List_Table is not loaded automatically so we need to load it in our application
if(!class_exists('WP_List_Table')) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

function dw_notice_error(string $msg): string {
	return '<div class="notice notice-error"><p>' . $msg . '</p></div>';
}

function dw_notice_info(string $msg): string {
	return '<div class="notice notice-info"><p>' . $msg . '</p></div>';
}

function dw_render_course_page() {
	$newProductsCount = 0;
	try {
		$newProductsCount = count(dw_import_products());
	} catch(Throwable $e) {
		dw_notice_error('Er is iets misgegaan bij het opslaan van het product. Herlaad de pagina en probeer het opnieuw.');
	}

	if($newProductsCount > 0) {
		echo dw_notice_info('Er zijn ' . $newProductsCount . ' nieuwe cursussen gesynchroniseerd met Dation');
	} else {
		echo dw_notice_info('Er zijn geen nieuwe cursussen gesynchroniseerd met Dation');
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
 * @return mixed[]
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

