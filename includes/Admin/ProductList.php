<?php
declare(strict_types=1);

namespace Dation\Woocommerce\Admin;

use WC_Product;
use WC_Product_Query;
use WP_List_Table;

class ProductList extends WP_List_Table {
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
			'id'       => 'Artikel (Webshop)',
			'location' => 'Locatie',
			'sku'      => 'Cursus (Dation)',
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
				return '<a target="_blank" href="'. get_edit_post_link($item[$column_name]). '">'. $item['name'] . '</a>';
			case 'location':
				return $item[$column_name];
			case 'stock':
				return $item[$column_name];
		}
	}
}