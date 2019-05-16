<?php
declare(strict_types=1);

const VARIABLES = [
	'virtual' => true,
	'manage_stock' => true,
	'sold_individually' =>true,
	'low_stock_amount' => 0,
];


/**
 * Add Admin menu item
 */
function dation_products_page() {
	add_menu_page(
		'Koppel Producten',
		'Dation-woocommerce',
		'manage_options',
		'dation_products',
		'dation_get_products',
		'',
		41
	);
}

add_action('admin_menu', 'dation_products_page');

function dation_get_products() {
	global $dw_options;
	$curl = curl_init();

	$addedProducts = 0;

	$today = new DateTime();
	curl_setopt_array($curl, array(
		CURLOPT_URL => "https://dashboard.dation.nl/api/v1/course-instances?startDateAfter=" . $today->format('Y-m-d'),
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => "",
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 30,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => "GET",
		CURLOPT_HTTPHEADER => array(
			"Accept: */*",
			'Authorization: Basic ' . $dw_options['api_key'],
			"Cache-Control: no-cache",
			"Connection: keep-alive",
			"Host: dashboard.dation.nl",
			"Postman-Token: a80eff2e-473e-4dc0-a88d-10e692a54d27,48664008-cb2b-4d9b-aece-f101f633d26b",
			"User-Agent: PostmanRuntime/7.13.0",
			"accept-encoding: gzip, deflate",
			"cache-control: no-cache",
			"cookie: PHPSESSID=03d87add6fd48ecdd289e00d061592f5"
		),
	));

	$response = curl_exec($curl);
	$err = curl_error($curl);

	curl_close($curl);

	if ($err) {
		echo "cURL Error #:" . $err;
	} else {
		foreach(json_decode($response, true) as $dationProduct) {
			if(get_product_by_sku($dationProduct['id']) === null) {
				$addedProducts += 1;
				add_woo_commerce_product($dationProduct);
				echo 'Nieuw terugkommoment toegevoegd';
			}
		}
		echo 'Er zijn ' . $addedProducts . ' nieuwe cursussen toegevoegd';
	}


}

function add_woo_commerce_product($dationProduct) {
	global $dw_options;
	$date = new DateTime($dationProduct['startDate']);

	$attributes = [
		'pa_datum' => [
			"name" => "pa_datum",
			"value" => $date->format('d-m-Y'),
			"position" => 1,
			"is_visible" => true,
			'is_taxonomy' => '1'
		],
		'pa_locatie' => [
			"name" => "pa_locatie",
			"value" => $dationProduct['parts'][0]['slots'][0]['city'],
			"is_visible" => true,
			'is_taxonomy' => '1'
		],
		'pa_tijd' => [
			"name" => "pa_tijd",
			"value" => $date->format('H:i'),
			"is_visible" => true,
			'is_taxonomy' => '1'
		]
	];


	$product = new WC_Product();

	$product->set_name($dationProduct['name'] . ' ' . $date->format('d-m-Y'));
	$product->set_description($dationProduct['name']);
	$product->set_short_description($dationProduct['ccvCode']);
	$product->set_sku($dationProduct['id']);
	$product->set_regular_price($dw_options['tkm_price']);
	$product->set_virtual(VARIABLES['virtual']);
	$product->set_manage_stock(VARIABLES['manage_stock']);
	$product->set_stock_quantity($dw_options['tkm_capacity']);
	$product->set_sold_individually(VARIABLES['sold_individually']);
	$product->set_low_stock_amount(VARIABLES['low_stock_amount']);
	$product->save();

	wp_set_object_terms($product->get_id(), $date->format('d-m-Y'), 'pa_datum', false);
	wp_set_object_terms($product->get_id(), $date->format('H:i'), 'pa_tijd', false);
	wp_set_object_terms($product->get_id(), $dationProduct['parts'][0]['slots'][0]['city'], 'pa_locatie', false);

	update_post_meta($product->get_id(), '_product_attributes', $attributes);

}

function get_product_by_sku( $sku ) {

	global $wpdb;

	$product_id = $wpdb->get_var( $wpdb->prepare( "SELECT post_id FROM $wpdb->postmeta WHERE meta_key='_sku' AND meta_value='%s' LIMIT 1", $sku ) );

	if ( $product_id ) return new WC_Product( $product_id );

	return null;
}

