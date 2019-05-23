<?php
declare(strict_types=1);

/**
 * Register a settings group for the plugin
 */
function dw_register_settings() {
	register_setting('dw_settings_group', 'dw_settings');
}

add_action('admin_init', 'dw_register_settings');

require 'dw-courses.php';

/**
 * Add Admin menu items
 */
function dw_admin_menu() {
	add_menu_page(
		'Instellingen',
		'Dation',
		'manage_options',
		'dation',
		'dw_render_options_page',
		plugins_url('images/dation-elephant-head-20x20.png', __FILE__),
		40
	);

	// Adding a submenu with the same slug tells Wordpress to not add a submenu for the parent item
	add_submenu_page(
		'dation',
		'Dation Instellingen',
		'Instellingen',
		'manage_options',
		'dation',
		'dw_render_options_page'
	);

	add_submenu_page(
		'dation',
		'Cursussen',
		'Cursussen',
		'manage_options',
		'dation-cursussen',
		'dw_render_course_page'
	);
}

add_action('admin_menu', 'dw_admin_menu');

require 'dw-options.php';
