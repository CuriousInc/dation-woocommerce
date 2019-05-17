<?php
declare(strict_types=1);

/**
 * Register a settings group for the plugin
 */
function dw_register_settings() {
	register_setting('dw_settings_group', 'dw_settings');
}

add_action('admin_init', 'dw_register_settings');

require 'dation-woocommerce-get-products.php';
require 'options.php';

/**
 * Add Admin menu items
 */
function dw_admin_menu() {
	add_menu_page(
		'Instellingen',
		'Dation',
		'manage_options',
		'dation',
		'dw_options_page_html',
		'',
		40
	);

	add_submenu_page(
		'dation',
		'Cursussen',
		'Cursussen',
		'manage_options',
		'dation-cursussen',
		'dw_get_products'
	);
}

add_action('admin_menu', 'dw_admin_menu');


