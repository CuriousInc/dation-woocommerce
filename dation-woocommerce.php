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
 * Version: 1.1.0
 */

// Global variables

$dw_options = get_option('dw_settings');

const DW_PLUGIN_FILE = __FILE__;

// Includes

require 'vendor/autoload.php';

require 'includes/cron-import-products.php';

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
	require_once 'includes/woocommerce-template.php';
}
