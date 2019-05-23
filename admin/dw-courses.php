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
	require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

function dw_show_course_page() {
	dw_get_products();

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

function dw_get_products() {
	$courses = dw_get_course_instances(new DateTime(), null) ?? [];

	$createdProducts = [];

	foreach($courses as $dationProduct) {
		if(dw_get_product_by_sku($dationProduct['id']) === null) {
			try{
				$product = dw_add_woo_commerce_product($dationProduct);
				$createdProducts[] = $product;
			} catch (Throwable $e) {
				echo '<div class="notice notice-error">
						<p>Er is iets misgegaan bij het opslaan van het product. Herlaad de pagina en probeer het opnieuw.</p>
					</div>';
			}
		}
	}

	if(count($createdProducts) > 0 ) {
		echo '<div class="notice notice-info"><p>Er zijn ' . count($createdProducts) . ' cursussen gesynchroniseerd met Dation</p></div>';
	}
}

/**
 * @param $dationProduct
 * @return WC_Product
 * @throws WC_Data_Exception
 */
function dw_add_woo_commerce_product($course) {
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

// Get product by unique `stock keep unit`.
// When creating a product, dation course ID is set to the sku of a woocommerce product.
// When synchronizing with Dation, we can use this to find Dation courses that are not in Woocommerce
function dw_get_product_by_sku($sku) {
	global $wpdb;

	$product_id = $wpdb->get_var( $wpdb->prepare("SELECT post_id FROM $wpdb->postmeta WHERE meta_key='_sku' AND meta_value='%s' LIMIT 1", $sku));

	if($product_id) {
		return new WC_Product($product_id);
	}

	return null;
}

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
		echo '<div class="error notice"><p>Er is iets misgegaan bij het synchroniseren van de producten. Herlaad de pagina en probeer het opnieuw</p></div>';
	} else {
		return json_decode($response, true);
	}

	return null;
}

class DationProductList extends WP_List_Table {
	public function prepare_items() {
		$columns = $this->get_columns();
		$hidden = $this->get_hidden_columns();
		$sortable = $this->get_sortable_columns();

		$data = $this->table_data();

		$perPage = 25;
		$currentPage = $this->get_pageNum();
		$totalItems = count($data);

		$this->set_pagination_args([
			'total_items' => $totalItems,
			'per_page' => $perPage
		]);

		$data = array_slice($data, (($currentPage -1) * $perPage), $perPage);

		$this->_column_headers = [$columns, $hidden, $sortable];
		$this->items = $data;
	}

	public function get_columns() {
		return [
			'id'       => 'Webshop Product',
			'location' => 'Locatie',
			'sku'      => 'Dation Product',
			'stock'    => 'Beschikbaar',
		];
	}

	public function get_hidden_columns() {
		return [];
	}

	public function get_sortable_columns() {
		return [
			[
				'id',
				true,
			]
		];
	}

	private function table_data() {
		$query = new WC_Product_Query();
		$products = $query->get_products();
		$data = [];
		/** @var WC_Product $product */
		foreach($products as $product) {
			$data[] = [
				'sku'      => $product->get_sku(),
				'id'       => $product->get_id(),
				'name'     => $product->get_name(),
				'location' => $product->get_attribute('pa_locatie'),
				'stock'    => $product->get_stock_quantity(),
			];
		}

		return $data;
	}

	public function column_default($item, $column_name) {
		global $dw_options;
		switch ($column_name) {
			case 'sku':
				return '<a target="_blank" href="https://dashboard.dation.nl/' . $dw_options['handle'] . '/nascholing/details?id='. $item[$column_name]. '">Openen in Dation</a>';
			case 'id':
				return '<a target="_blank" href="'. get_admin_url() . 'post.php?post='. $item[$column_name] . '&action=edit">'. $item['name'] . '</a>';
			case 'location':
				return $item[$column_name];
			case 'stock':
				return $item[$column_name];
		}
	}
}
