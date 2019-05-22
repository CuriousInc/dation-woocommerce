<?php

/**
 * Template Function Overrides
 */

// Fields
const ISSUE_DATE_DRIVING_LICENSE = 'Afgiftedatum_Rijbewijs';
const DATE_OF_BIRTH              = 'Geboortedatum';
const NATIONAL_REGISTRY_NUMBER   = 'Rijksregisternummer';
const AUTOMATIC_TRANSMISSION     = 'Automaat';

const BELGIAN_DATE_FORMAT =  'd.m.Y';

// Register override for checkout and order email
add_filter('woocommerce_checkout_fields', 'dw_override_checkout_fields');
add_filter('woocommerce_email_order_meta_keys', 'custom_order_meta_fields');

function custom_order_meta_fields($keys) {
	$keys[] = ISSUE_DATE_DRIVING_LICENSE;
	$keys[] = DATE_OF_BIRTH;
	$keys[] = NATIONAL_REGISTRY_NUMBER;
	$keys[] = AUTOMATIC_TRANSMISSION;

	return $keys;
}
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

	$newOrderFields['order'][AUTOMATIC_TRANSMISSION] = [
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
	if(!dw_is_valid_date($_POST[DATE_OF_BIRTH])) {
		wc_add_notice(__('Geboortedatum is onjuist, verwacht formaat \'d.m.Y\''), 'error');
	} else {
		$birthDate = DateTime::createFromFormat(BELGIAN_DATE_FORMAT, $_POST[DATE_OF_BIRTH]);
		if(!dw_is_valid_rrn($_POST[NATIONAL_REGISTRY_NUMBER], $birthDate)) {
		wc_add_notice(__('Rijksregsternummer is onjuist'), 'error');
	}}

	if(!dw_is_valid_date($_POST[ISSUE_DATE_DRIVING_LICENSE])) {
		wc_add_notice(__('Afgiftedatum rijbewijs is onjuist, verwacht formaat \'d.m.Y\''), 'error');
	}
}

function dw_is_valid_date(string $input): bool {
	$dateTime = DateTime::createFromFormat(BELGIAN_DATE_FORMAT, $input);
	return $dateTime && $dateTime->format(BELGIAN_DATE_FORMAT) === $input;
}

function dw_is_valid_rrn(string $nationalRegistryNumber, DateTime $birthDate): bool {
	if(
		substr($nationalRegistryNumber, 0, 2) != $birthDate->format('y')
		|| substr($nationalRegistryNumber, 2, 2) != $birthDate->format('m')
		|| substr($nationalRegistryNumber, 4, 2) != $birthDate->format('d')
	) {
		return false;
	}
	if($birthDate->format('Y') < 2000) {
		if((97 - (substr($nationalRegistryNumber, 0, 9) % 97)) != intval(substr($nationalRegistryNumber, -2))) {
			return false;
		}
	} else {
		//checksum + 2000000000 (http://www.ibz.rrn.fgov.be/fileadmin/user_upload/nl/rr/toegang/bestand-rr.pdf)
		if((97 - (intval('2' . substr($nationalRegistryNumber, 0, 9)) % 97)) != intval(substr($nationalRegistryNumber, -2))) {
			return false;
		}
	}

	return true;
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
		AUTOMATIC_TRANSMISSION
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
add_action('woocommerce_admin_order_data_after_shipping_address', 'dw_admin_order_tkm_data', 10, 1);

function dw_admin_order_tkm_data($order) {
	echo '<p><strong>' . __('Geboortedatum') . ':</strong> <br/>'
		. get_post_meta($order->get_id(), DATE_OF_BIRTH, true) . '</p>';
	echo '<p><strong>' . __('Rijksregisternummer') . ':</strong> <br/>'
		. get_post_meta($order->get_id(), NATIONAL_REGISTRY_NUMBER, true) . '</p>';
	echo '<p><strong>' . __('Afgiftedatum rijbewijs') . ':</strong> <br/>'
		. get_post_meta($order->get_id(), ISSUE_DATE_DRIVING_LICENSE, true) . '</p>';
	echo '<p><strong>' . __('Automaat') . ':</strong> <br/>'
		. (get_post_meta($order->get_id(), AUTOMATIC_TRANSMISSION, true) ? __('Ja') : __('Nee')) . '</p>';
}