<?php
/*
Plugin Name: Dation Woocommerce
Plugin URI: http:/www.dation.nl/
Description: Dation Woocommerce plugin
Author: Dation
Author URI: http://www.dation.nl
Version: 0.0.1
*/

function dation_options_page_html(){
	?>
	<div class="wrap">
		<h1><?php esc_html( get_admin_page_title() ); ?> - Welkom!</h1>
	</div>
	<?php
}

function dation_options_page() {
	add_menu_page(
		'Dation Instellingen',
		'Dation',
		'manage_options',
		'dation',
		'dation_options_page_html',
		'',
		40
	);
}
add_action( 'admin_menu', 'dation_options_page' );


/**
 * Check if WooCommerce is active
 */
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

	if ( ! class_exists( 'WC_Acme' ) ) {

		/**
		 * Localisation
		 **/
		load_plugin_textdomain( 'wc_acme', false, dirname( plugin_basename( __FILE__ ) ) . '/' );

		class WC_Acme {
			public function __construct() {
				// called only after woocommerce has finished loading
				add_action( 'woocommerce_init', array( &$this, 'woocommerce_loaded' ) );

				// called after all plugins have loaded
				add_action( 'plugins_loaded', array( &$this, 'plugins_loaded' ) );

				// called just before the woocommerce template functions are included
				add_action( 'init', array( &$this, 'include_template_functions' ), 20 );

				// indicates we are running the admin
				if ( is_admin() ) {
					// ...
				}

				// indicates we are being served over ssl
				if ( is_ssl() ) {
					// ...
				}

				// take care of anything else that needs to be done immediately upon plugin instantiation, here in the constructor
			}

			/**
			 * Take care of anything that needs woocommerce to be loaded.
			 * For instance, if you need access to the $woocommerce global
			 */
			public function woocommerce_loaded() {
				// ...
			}

			/**
			 * Take care of anything that needs all plugins to be loaded
			 */
			public function plugins_loaded() {
				// ...
			}

			/**
			 * Override any of the template functions from woocommerce/woocommerce-template.php
			 * with our own template functions file
			 */
			public function include_template_functions() {
				include( 'includes/woocommerce-template.php' );
			}
		}

		// finally instantiate our plugin class and add it to the set of globals
		$GLOBALS['wc_acme'] = new WC_Acme();
	}
}
