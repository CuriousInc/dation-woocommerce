<?php

/**
 * Template Function Overrides
 *
 */

const ISSUE_DATE_DRIVING_LICENSE = 'issue_date_driving_license';
const DATE_OF_BIRTH              = 'date_of_birth';
const NATIONAL_REGISTRY_NUMBER   = 'rijksregisternummer';
const AUTOMATIC_GEARS            = 'automatic_gears';

// Register override
add_filter('woocommerce_checkout_fields', 'dw_override_checkout_fields');

// Override checkout fields, add custom TKM fields to checkout fields
function dw_override_checkout_fields($fields) {

	$newOrderFields['order'][DATE_OF_BIRTH] = [
		'type'     => 'text',
		'label'    => __('Geboortedatum'),
		'required' => true,
	];

	$newOrderFields['order'][NATIONAL_REGISTRY_NUMBER] = [
		'type'     => 'text',
		'label'    => __('Rijksregisternummer'),
		'required' => true,
	];

	$newOrderFields['order'][ISSUE_DATE_DRIVING_LICENSE] = [
		'type'     => 'text',
		'label'    => __('Afgiftedatum rijbewijs'),
		'required' => true,
	];

	$newOrderFields['order'][AUTOMATIC_GEARS] = [
		'type'     => 'select',
		'options'  => [
			'yes' => __('Yes'),
			'no'  => __('No')
		],
		'label'    => __('Ik rij met automatische versnellingen'),
		'required' => true,
	];

	// Merge arrays at the 'order' key
	$fields['order'] = array_merge($newOrderFields['order'], $fields['order']);

	return $fields;
}

/**
 * Process the checkout
 */
add_action('woocommerce_checkout_process', 'dw_process_checkout');

function dw_process_checkout() {

	// // Check if set, if its not set add an error.
	// if(!$_POST[DATE_OF_BIRTH]) {
	// 	wc_add_notice(__('Geboorte 2 is compulsory. Please enter a value'), 'error');
	// }

}

/**
 * Update the order meta with field value
 */
add_action('woocommerce_checkout_update_order_meta', 'dw_checkout_update_order_meta');

function dw_checkout_update_order_meta($order_id) {
	$fields = [
		ISSUE_DATE_DRIVING_LICENSE,
		DATE_OF_BIRTH,
		NATIONAL_REGISTRY_NUMBER,
		AUTOMATIC_GEARS
	];

	foreach($fields as $field) {
		if(!empty($_POST[$field])) {
			update_post_meta($order_id, $field, sanitize_text_field($_POST[$field]));
		}
	}
}

/**
 * Display field value on the order edit page
 */
add_action('woocommerce_admin_order_data_after_billing_address', 'dw_admin_order_tkm_data', 10, 1);

function dw_admin_order_tkm_data($order) {
	echo '<p><strong>' . __('Geboortedatum') . ':</strong> <br/>'
		. get_post_meta($order->get_id(), DATE_OF_BIRTH, true) . '</p>';
	echo '<p><strong>' . __('Rijksregisternummer') . ':</strong> <br/>'
		. get_post_meta($order->get_id(), NATIONAL_REGISTRY_NUMBER, true) . '</p>';
	echo '<p><strong>' . __('Afgiftedatum rijbewijs') . ':</strong> <br/>'
		. get_post_meta($order->get_id(), ISSUE_DATE_DRIVING_LICENSE, true) . '</p>';
	echo '<p><strong>' . __('Automaat') . ':</strong> <br/>'
		. get_post_meta($order->get_id(), AUTOMATIC_GEARS, true) . '</p>';
}