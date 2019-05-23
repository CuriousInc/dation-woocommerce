<?php
declare(strict_types=1);

/*
Plugin Name: Dation Woocommerce
Plugin URI: http:/www.dation.nl/
Description: Dation Woocommerce plugin
Author: Dation
Author URI: http://www.dation.nl
Version: 0.0.3
*/

// Global variables

$dw_options = get_option('dw_settings');

// Includes

require 'vendor/autoload.php';

/**
 * Localisation
 **/
load_plugin_textdomain('dw', false, dirname(plugin_basename(__FILE__)) . '/');

// called just before the woocommerce template functions are included
add_action('init', 'dw_override_woo_templates', 20);

// indicates we are running the admin
if(is_admin()) {
	require 'admin/dation-woocommerce-admin.php';
}

/**
 * Override any of the template functions from woocommerce/woocommerce-template.php
 * with our own template functions file
 */
function dw_override_woo_templates() {
	include 'includes/woocommerce-template.php';
}