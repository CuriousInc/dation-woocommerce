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

/**
 * Add Admin menu items
 */
function dw_admin_menu() {
	add_menu_page(
		'Instellingen',
		'Dation',
		'manage_options',
		'dw-options.php',
		'',
		'',
		40
	);

	// Adding a submenu with the same slug tells Wordpress to not add a submenu for the parent item
	add_submenu_page(
		'dw-options.php',
		'Instellingen',
		'Instellingen',
		'manage_options',
		'dw-options.php'
	);

	add_submenu_page(
		'dw-options.php',
		'Cursussen',
		'Cursussen',
		'manage_options',
		'dation-cursussen',
		'dw_get_products'
	);
}

add_action('admin_menu', 'dw_admin_menu');

require 'dw-options.php';
