<?php

/**
 * Template Function Overrides
 */

use Dation\Woocommerce\Adapter\OrderManagerFactory;
use SetBased\Rijksregisternummer\Rijksregisternummer;
use SetBased\Rijksregisternummer\RijksregisternummerHelper;

// Fields
const DW_ISSUE_DATE_DRIVING_LICENSE = 'Afgiftedatum_Rijbewijs';
const DW_DATE_OF_BIRTH              = 'Geboortedatum';
const DW_NATIONAL_REGISTRY_NUMBER   = 'Rijksregisternummer';
const DW_AUTOMATIC_TRANSMISSION     = 'Automaat';

const DW_BELGIAN_DATE_FORMAT = 'd.m.Y';
const DW_API_DATE_FORMAT = 'Y-m-d';

// Register override for checkout and order email
add_filter('woocommerce_checkout_fields', 'dw_override_checkout_fields');
add_filter('woocommerce_email_order_meta', 'dw_email_order_render_extra_fields', 10, 3);

/**
 * @param WC_Order $order
 * @param bool $sent_to_admin
 * @param bool $plain_text
 */
function dw_email_order_render_extra_fields($order, $sent_to_admin, $plain_text) {
	$issueDrivingLicense    = get_post_meta($order->get_id(), DW_ISSUE_DATE_DRIVING_LICENSE, true);
	$dateOfBirth            = get_post_meta($order->get_id(), DW_DATE_OF_BIRTH, true);
	$nationalRegistryNumber = get_post_meta($order->get_id(), DW_NATIONAL_REGISTRY_NUMBER, true);
	$automaticTransmission  = get_post_meta($order->get_id(), DW_AUTOMATIC_TRANSMISSION, true);

	if(!$plain_text) {
		echo '<h2>Extra informatie</h2>
				<ul>
					<li><strong>Afgiftedatum rijbewijs</strong> ' . $issueDrivingLicense . '</li>
					<li><strong>Geboortedatum</strong> ' . $dateOfBirth . '</li>
					<li><strong>Rijksregisternummer</strong> ' . RijksregisternummerHelper::format($nationalRegistryNumber) . '</li>
					<li><strong>Automaat</strong> ' . $automaticTransmission === 'no' ? 'Nee' : 'Ja' . '</li>
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
	if(!empty($_POST[DW_DATE_OF_BIRTH])) {
		if(!dw_is_valid_date($_POST[DW_DATE_OF_BIRTH])) {
			wc_add_notice(__('Geboortedatum is onjuist, verwacht formaat dd.mm.yyyy'), 'error');
			$invalidDate = true;
		}
	}

	if(!empty($_POST[DW_NATIONAL_REGISTRY_NUMBER])) {
		if(!dw_is_valid_national_registry_number_format($_POST[DW_NATIONAL_REGISTRY_NUMBER])) {
			wc_add_notice(__('Rijksregsternummer is onjuist'), 'error');
		} elseif(
			!$invalidDate
			&& !dw_is_match_national_registry_number_and_birth_date(
				$_POST[DW_NATIONAL_REGISTRY_NUMBER],
				DateTime::createFromFormat(DW_BELGIAN_DATE_FORMAT, $_POST[DW_DATE_OF_BIRTH])
			)
		) {
			wc_add_notice(__('Rijksregsternummer komt niet overeen met geboortedatum'), 'error');
		}
	}

	if(!empty($_POST[DW_ISSUE_DATE_DRIVING_LICENSE])) {
		if(!dw_is_valid_date($_POST[DW_ISSUE_DATE_DRIVING_LICENSE])) {
			wc_add_notice(__('Afgiftedatum rijbewijs is onjuist, verwacht formaat dd.mm.yyyy'), 'error');
		}
	}
}

function dw_is_valid_date(string $input): bool {
	$dateTime = DateTime::createFromFormat(DW_BELGIAN_DATE_FORMAT, $input);
	return $dateTime && $dateTime->format(DW_BELGIAN_DATE_FORMAT) === $input;
}

function dw_is_valid_national_registry_number_format(string $registryNumberString): bool {
	try {
		$registryNumber = new Rijksregisternummer($registryNumberString);
	} catch(UnexpectedValueException $exception) {
		// Invalid format
		return false;
	}
	return true;
}

function dw_is_match_national_registry_number_and_birth_date(
	string $registryNumberString,
	DateTime $birthDate
): bool {
	$registryNumber = new Rijksregisternummer($registryNumberString);
	return $registryNumber->getBirthday() === $birthDate->format('Y-m-d');
}

/**
 * Update the order meta with field value
 */
add_action('woocommerce_checkout_update_order_meta', 'dw_checkout_update_order_meta');

function dw_checkout_update_order_meta($orderId) {
	$fields = [
		DW_ISSUE_DATE_DRIVING_LICENSE,
		DW_DATE_OF_BIRTH,
		DW_NATIONAL_REGISTRY_NUMBER,
		DW_AUTOMATIC_TRANSMISSION
	];

	foreach($fields as $field) {
		if(!empty($_POST[$field])) {
			update_post_meta($orderId, $field, sanitize_text_field($_POST[$field]));
		}
	}
}

/**
 * Display field value on the order edit page
 */
add_action('woocommerce_admin_order_data_after_shipping_address', 'dw_admin_order_render_extra_fields', 10, 1);

/**
 * Render extra fields for admin order page
 *
 * @param WC_Order $order
 */
function dw_admin_order_render_extra_fields($order) {
	echo '<p><strong>' . __('Geboortedatum') . ':</strong> <br/>'
		. get_post_meta($order->get_id(), DW_DATE_OF_BIRTH, true) . '</p>';
	echo '<p><strong>' . __('Rijksregisternummer') . ':</strong> <br/>'
		. get_post_meta($order->get_id(), DW_NATIONAL_REGISTRY_NUMBER, true) . '</p>';
	echo '<p><strong>' . __('Afgiftedatum rijbewijs') . ':</strong> <br/>'
		. get_post_meta($order->get_id(), DW_ISSUE_DATE_DRIVING_LICENSE, true) . '</p>';
	echo '<p><strong>' . __('Automaat') . ':</strong> <br/>'
		. (get_post_meta($order->get_id(), DW_AUTOMATIC_TRANSMISSION, true) ? __('Ja') : __('Nee')) . '</p>';
}

add_action('woocommerce_order_status_processing', 'dw_woocommerce_order_status_processing', 10, 1);

function dw_woocommerce_order_status_processing($orderId) {
	$order = wc_get_order($orderId);
	$orderManager = OrderManagerFactory::getManager();
	$orderManager->procesOrder($order);
}

add_action('woocommerce_order_actions', 'dw_order_meta_box_actions');

function dw_order_meta_box_actions($actions) {
    $actions['dw_send_student_to_dashboard'] = __('Leerling aanmaken in Dation');

    return $actions;
}

add_action('woocommerce_order_action_dw_send_student_to_dashboard', 'dw_send_student_to_dashboard');

function dw_send_student_to_dashboard(WC_Order $order) {
    $orderManager = OrderManagerFactory::getManager();
    $orderManager->procesOrder($order);
}


/**
 *  Add a custom email to the list of emails WooCommerce should load
 *
 * @since 0.1
 * @param array $email_classes available email classes
 * @return array filtered available email classes
 */
function dw_add_synchornizing_failed_email( $email_classes ) {
	if($email_classes === '') {
		$email_classes = [];
	}
	require(__DIR__ . '/emails/DationStudentFailedEmail.php' );
	// add the email class to the list of email classes that WooCommerce loads
	$email_classes['DW_Student_Failed_Email'] = new DationStudentFailedEmail();

	return $email_classes;

}
add_filter( 'woocommerce_email_classes', 'dw_add_synchornizing_failed_email', 10, 1 );