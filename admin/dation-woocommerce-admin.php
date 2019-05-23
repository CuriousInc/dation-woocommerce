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
		'dation',
		'dw_options_page_html',
		'',
		40
	);

	// Adding a submenu with the same slug tells Wordpress to not add a submenu for the parent item
	add_submenu_page(
		'dation',
		'Dation Instellingen',
		'Instellingen',
		'manage_options',
		'dation',
		'dw_options_page_html'
	);

	add_submenu_page(
		'dation',
		'Cursussen',
		'Cursussen',
		'manage_options',
		'dation-cursussen',
		'dw_show_course_page'
	);
}

add_action('admin_menu', 'dw_admin_menu');

require 'dw-options.php';
