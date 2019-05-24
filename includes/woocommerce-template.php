<?php

/**
 * Template Function Overrides
 */

use SetBased\Rijksregisternummer\Rijksregisternummer;

// Fields
const DW_ISSUE_DATE_DRIVING_LICENSE = 'Afgiftedatum_Rijbewijs';
const DW_DATE_OF_BIRTH              = 'Geboortedatum';
const DW_NATIONAL_REGISTRY_NUMBER   = 'Rijksregisternummer';
const DW_AUTOMATIC_TRANSMISSION     = 'Automaat';

const DW_BELGIAN_DATE_FORMAT =  'd.m.Y';

// Register override for checkout and order email
add_filter('woocommerce_checkout_fields', 'dw_override_checkout_fields');
add_filter('woocommerce_email_order_meta', 'dw_custom_order_meta_fields', 10, 3);

/**
 * @param WC_Product $order_obj
 * @param $sent_to_admin
 * @param $plain_text
 */
function dw_custom_order_meta_fields($order_obj, $sent_to_admin, $plain_text) {
	$issueDrivingLicense = get_post_meta($order_obj->get_id(), DW_ISSUE_DATE_DRIVING_LICENSE, true);
	$dateOfBirth = get_post_meta($order_obj->get_id(), DW_DATE_OF_BIRTH, true);
	$nationalRegistryNumber = get_post_meta($order_obj->get_id(), DW_NATIONAL_REGISTRY_NUMBER, true);
	$automaticTransmission = get_post_meta($order_obj->get_id(), DW_AUTOMATIC_TRANSMISSION, true);

	if(!$plain_text) {
		echo '<h2>Extra informatie</h2>
				<ul>
					<li><strong>Afgiftedatum rijbewijs</strong> ' . $issueDrivingLicense . '</li>
					<li><strong>Geboortedatum</strong> ' . $dateOfBirth . '</li>
					<li><strong>Rijksregisternummer</strong> ' . $nationalRegistryNumber . '</li>
					<li><strong>Automaat</strong> ' . __($automaticTransmission) . '</li>
				</ul>';
	} else {
		echo "EXTRA INFORMATIE\n
				Afgiftedatum rijbewijs: $issueDrivingLicense
				Geboortedatum: $dateOfBirth
				Rijksregisternummer: $nationalRegistryNumber
				Automaat: $automaticTransmission";
	}
}

// Override checkout fields, add custom TKM fields to checkout fields
function dw_override_checkout_fields($fields) {
	unset($fields['shipping']['shipping_address_2']);
	unset($fields['shipping']['shipping_company']);

	unset($fields['billing']['billing_address_2']);
	unset($fields['billing']['billing_company']);

	$newOrderFields['order'][DW_DATE_OF_BIRTH] = [
		'type'     => 'text',
		'label'    => __('Geboortedatum'),
		'required' => true,
	];

	$newOrderFields['order'][DW_NATIONAL_REGISTRY_NUMBER] = [
		'type'     => 'text',
		'label'    => __('Rijksregisternummer'),
		'required' => true,
	];

	$newOrderFields['order'][DW_ISSUE_DATE_DRIVING_LICENSE] = [
		'type'     => 'text',
		'label'    => __('Afgiftedatum rijbewijs'),
		'required' => true,
	];

	$newOrderFields['order'][DW_AUTOMATIC_TRANSMISSION] = [
		'type'     => 'select',
		'options'  => [
			'no'  => __('No'),
			'yes' => __('Yes'),
		],
		'label'    => __('Ik rijd enkel met een automaat'),
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
	if(!dw_is_valid_date($_POST[DW_DATE_OF_BIRTH])) {
		wc_add_notice(__('Geboortedatum is onjuist, verwacht formaat dd.mm.yyyy'), 'error');
	} else {
		$birthDate = DateTime::createFromFormat(DW_BELGIAN_DATE_FORMAT, $_POST[DW_DATE_OF_BIRTH]);
		if(!dw_is_valid_national_registry_number($_POST[DW_NATIONAL_REGISTRY_NUMBER], $birthDate)) {
			wc_add_notice(__('Rijksregsternummer is onjuist'), 'error');
		}}

	if(!dw_is_valid_date($_POST[DW_ISSUE_DATE_DRIVING_LICENSE])) {
		wc_add_notice(__('Afgiftedatum rijbewijs is onjuist, verwacht formaat dd.mm.yyyy'), 'error');
	}
}

function dw_is_valid_date(string $input): bool {
	$dateTime = DateTime::createFromFormat(DW_BELGIAN_DATE_FORMAT, $input);
	return $dateTime && $dateTime->format(DW_BELGIAN_DATE_FORMAT) === $input;
}

function dw_is_valid_national_registry_number(string $registryNumberString, DateTime $birthDate): bool {
	try {
		$registryNumber = new Rijksregisternummer($registryNumberString);
	} catch (UnexpectedValueException $exception) {
		// Invalid format
		return false;
	}

	if($registryNumber->getBirthday() !== $birthDate->format('Y-m-d')) {
		// Birth date in number mismatches
		return false;
	}

	return true;
}

/**
 * Update the order meta with field value
 */
add_action('woocommerce_checkout_update_order_meta', 'dw_checkout_update_order_meta');

function dw_checkout_update_order_meta($order_id) {
	$fields = [
		DW_ISSUE_DATE_DRIVING_LICENSE,
		DW_DATE_OF_BIRTH,
		DW_NATIONAL_REGISTRY_NUMBER,
		DW_AUTOMATIC_TRANSMISSION
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
		. get_post_meta($order->get_id(), DW_DATE_OF_BIRTH, true) . '</p>';
	echo '<p><strong>' . __('Rijksregisternummer') . ':</strong> <br/>'
		. get_post_meta($order->get_id(), DW_NATIONAL_REGISTRY_NUMBER, true) . '</p>';
	echo '<p><strong>' . __('Afgiftedatum rijbewijs') . ':</strong> <br/>'
		. get_post_meta($order->get_id(), DW_ISSUE_DATE_DRIVING_LICENSE, true) . '</p>';
	echo '<p><strong>' . __('Automaat') . ':</strong> <br/>'
		. (get_post_meta($order->get_id(), DW_AUTOMATIC_TRANSMISSION, true) ? __('Ja') : __('Nee')) . '</p>';
}