<?php

declare(strict_types=1);

/**
 * Dation Woocommerce plugin
 *
 * @wordpress-plugin
 * Plugin Name: Dation Woocommerce
 * Description: Je website altijd up-to-date met je Dation planning. Importeer je Dation cursussen als Woocommerce producten.
 * Author: Dation
 * Author URI: http://www.dation.nl
 * Version: 1.2.47
 */

// Global variables

use Dation\Woocommerce\ApiEndpoints\LeadContactFormEndpoint;

$dw_options = get_option('dw_settings');

const DW_PLUGIN_FILE = __FILE__;

if(!defined('DW_BASE_HOST')) {
	define('DW_BASE_HOST', 'https://dashboard.dation.nl');
}

// Includes

require 'vendor/autoload.php';

require 'includes/cron-import-products.php';

/**
 * Localisation
 **/
load_plugin_textdomain('dw', false, dirname(plugin_basename(__FILE__)) . '/');

// called just before the woocommerce template functions are included
add_action('init', 'dw_override_woo_templates', 20);
$redirectUrl = isset($dw_options['redirect-url']) && !empty($dw_options['redirect-url']) ? $dw_options['redirect-url'] : null;
$endpoint    = new LeadContactFormEndpoint($redirectUrl);
add_action('rest_api_init', [$endpoint, 'register_routes']);

// indicates we are running the admin
if(is_admin()) {
	require 'admin/dation-woocommerce-admin.php';
}

/**
 * Override any of the template functions from woocommerce/woocommerce-template.php
 * with our own template functions file
 */
function dw_override_woo_templates() {
	if(!session_id()) {
		session_start();
	}

	require_once 'includes/woocommerce-template.php';
}
