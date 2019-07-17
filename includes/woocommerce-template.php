<?php

declare(strict_types=1);

/**
 * Template Function Overrides
 */

use Dation\Woocommerce\Adapter\OrderManager;
use Dation\Woocommerce\Adapter\OrderManagerFactory;
use Dation\Woocommerce\Email\EmailSyncFailed;
use Dation\Woocommerce\Exceptions\LicenseDateOverTimeException;
use Dation\Woocommerce\Exceptions\LicenseDateUnderTimeException;
use SetBased\Rijksregisternummer\Rijksregisternummer;
use SetBased\Rijksregisternummer\RijksregisternummerHelper;

const TOO_EARLY_MESSAGE     = "Het gekozen terugkommoment is te vroeg. Kies een terugkommoment tussen de 6 en 9 maanden na de afgiftedatum van uw rijbewijs.";
const OVERTIME_MESSAGE      = "Let op: als u geen uitstel heeft gekregen van de overheid dient u een boete van 51 euro te betalen. Kies een terugkommoment tussen de 6 en 9 maanden na de afgiftedatum van uw rijbewijs om dit te voorkomen. U kunt er ook voor kiezen om toch door te gaan met uw huidige keuze.";
const LONG_OVERTIME_MESSAGE = "Let op: als u geen uitstel heeft gekregen van de overheid bestaat de kans dat u een boete van 51 euro moet betalen of dat u helemaal niet mag deelnemen aan het terugkommoment op deze datum. Kies een terugkommoment tussen de 6 en 9 maanden na de afgiftedatum van uw rijbewijs om dit te voorkomen. U kunt er ook voor kiezen om toch door te gaan met uw huidige keuze.";
const DW_WARNING            = "dw_warning_given";

// Register override for checkout and order email
add_filter('woocommerce_checkout_fields', 'dw_override_checkout_fields');
add_filter('woocommerce_email_order_meta', 'dw_email_order_render_extra_fields', 10, 3);

/**
 * @param WC_Order $order
 * @param bool $sent_to_admin
 * @param bool $plain_text
 */
function dw_email_order_render_extra_fields($order, $sent_to_admin, $plain_text) {
	$issueDrivingLicense    = get_post_meta($order->get_id(), OrderManager::KEY_ISSUE_DATE_DRIVING_LICENSE, true);
	$dateOfBirth            = get_post_meta($order->get_id(), OrderManager::KEY_DATE_OF_BIRTH, true);
	$nationalRegistryNumber = get_post_meta($order->get_id(), OrderManager::KEY_NATIONAL_REGISTRY_NUMBER, true);
	$automaticTransmission  = get_post_meta($order->get_id(), OrderManager::KEY_AUTOMATIC_TRANSMISSION, true) === 'no' ? 'Nee' : 'Ja' ;
	$hasReceivedLetter      = get_post_meta($order->get_id(), OrderManager::KEY_HAS_RECEIVED_LETTER, true);

	if($hasReceivedLetter === "no") {
		$receivedLetterListItem = '<li style="color: red"><strong>Brief ontvangen</strong> Nee</li>';
	} else {
		$receivedLetterListItem = '<li><strong>Brief ontvangen</strong> Ja</li>';
	}
	$issueDrivingLicenseDateWarning = "";
	try {
		canFollowMoment($issueDrivingLicense);
	} catch (LicenseDateOverTimeException $e) {
		//Add warning
		$issueDrivingLicenseDateWarning = '<p style="color: red">Let op: TKM later dan 9 maanden</p>';
	} catch (LicenseDateUnderTimeException $e) {
		//This should never happen
		$issueDrivingLicenseDateWarning = '<p style="color: red">Let op: TKM eerder dan 6 maanden</p>';
	}

	if($sent_to_admin) {
		if(!$plain_text) {
			echo '<h2>Extra informatie</h2>
				<ul>
					<li><strong>Afgiftedatum rijbewijs</strong> ' . $issueDrivingLicense . '</li>
					<li><strong>Geboortedatum</strong> ' . $dateOfBirth . '</li>
					<li><strong>Rijksregisternummer</strong> ' . RijksregisternummerHelper::format($nationalRegistryNumber) . '</li>
					<li><strong>Automaat</strong> ' . $automaticTransmission . '</li>
					' . $receivedLetterListItem . '
				</ul>';
			if($issueDrivingLicenseDateWarning !== "") {
				echo $issueDrivingLicenseDateWarning;
			}
		} else {
			echo "EXTRA INFORMATIE\n
				Afgiftedatum rijbewijs: $issueDrivingLicense
				Geboortedatum: $dateOfBirth
				Rijksregisternummer: $nationalRegistryNumber
				Automaat: $automaticTransmission
				Brief Ontvangen: $hasReceivedLetter";
			if($issueDrivingLicenseDateWarning !== "") {
				echo $issueDrivingLicenseDateWarning;
			}
		}
	}
}

// Override checkout fields, add custom TKM fields to checkout fields
function dw_override_checkout_fields($fields) {
	unset($fields['shipping']['shipping_address_2']);
	unset($fields['shipping']['shipping_company']);

	unset($fields['billing']['billing_address_2']);
	unset($fields['billing']['billing_company']);

	$newOrderFields['order'][OrderManager::KEY_DATE_OF_BIRTH] = [
		'type'     => 'text',
		'label'    => __('Geboortedatum'),
		'required' => true,
	];

	$newOrderFields['order'][OrderManager::KEY_NATIONAL_REGISTRY_NUMBER] = [
		'type'     => 'text',
		'label'    => __('Rijksregisternummer'),
		'required' => true,
	];

	$newOrderFields['order'][OrderManager::KEY_ISSUE_DATE_DRIVING_LICENSE] = [
		'type'     => 'text',
		'label'    => __('Afgiftedatum rijbewijs'),
		'required' => true,
	];

	$newOrderFields['order'][OrderManager::KEY_AUTOMATIC_TRANSMISSION] = [
		'type'     => 'select',
		'options'  => [
			'no'  => __('No'),
			'yes' => __('Yes'),
		],
		'label'    => __('Ik rijd enkel met een automaat'),
		'required' => true,
	];

	$newOrderFields['order'][OrderManager::KEY_HAS_RECEIVED_LETTER] = [
		'type'     => 'select',
		'label'    => 'Ik heb een oproepbrief ontvangen van de overheid om een terugkommoment te volgen',
		'options'  => [
			''    => __(''),
			'no'  => __('No'),
			'yes' => __('Yes'),
		],
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
	$driverLicenseIssueDate = $_POST[OrderManager::KEY_ISSUE_DATE_DRIVING_LICENSE];
	if(!empty($driverLicenseIssueDate)) {
		if(!dw_is_valid_date($driverLicenseIssueDate)) {
			wc_add_notice(__('Afgiftedatum rijbewijs is onjuist, verwacht formaat dd.mm.yyyy'), 'error');
		} else {
			try {
				canFollowMoment($driverLicenseIssueDate);
			} catch (LicenseDateOverTimeException $e) {
				if(empty($_SESSION[OrderManager::KEY_ISSUE_DATE_DRIVING_LICENSE]) || $_SESSION[OrderManager::KEY_ISSUE_DATE_DRIVING_LICENSE] === $driverLicenseIssueDate) {
					//If the dat is the same, and we have nog
					if($_SESSION[DW_WARNING] !== true) {
						$_SESSION[DW_WARNING] = true;
						$_SESSION[OrderManager::KEY_ISSUE_DATE_DRIVING_LICENSE] = $driverLicenseIssueDate;

						wc_add_notice(__($e->getMessage()), "error");
					}
				} else {
					//If the date is different then before give the warning again.
					wc_add_notice(__($e->getMessage()), "error");
					$_SESSION[OrderManager::KEY_ISSUE_DATE_DRIVING_LICENSE] = $driverLicenseIssueDate;
				}
			} catch (LicenseDateUnderTimeException $e) {
				wc_add_notice(__($e->getMessage()), "error");
			}
		}
	}

	if(!empty($_POST[OrderManager::KEY_DATE_OF_BIRTH])) {
		if(!dw_is_valid_date($_POST[OrderManager::KEY_DATE_OF_BIRTH])) {
			wc_add_notice(__('Geboortedatum is onjuist, verwacht formaat dd.mm.yyyy'), 'error');
			$invalidDate = true;
		}
	}

	if(!empty($_POST[OrderManager::KEY_NATIONAL_REGISTRY_NUMBER])) {
		if(!dw_is_valid_national_registry_number_format($_POST[OrderManager::KEY_NATIONAL_REGISTRY_NUMBER])) {
			wc_add_notice(__('Rijksregsternummer is onjuist'), 'error');
		} elseif(
			!$invalidDate
			&& !dw_is_match_national_registry_number_and_birth_date(
				$_POST[OrderManager::KEY_NATIONAL_REGISTRY_NUMBER],
				DateTime::createFromFormat(OrderManager::BELGIAN_DATE_FORMAT, $_POST[OrderManager::KEY_DATE_OF_BIRTH])
			)
		) {
			wc_add_notice(__('Rijksregsternummer komt niet overeen met geboortedatum'), 'error');
		}
	}
}

function dw_is_valid_date(string $input): bool {
	$dateTime = DateTime::createFromFormat(OrderManager::BELGIAN_DATE_FORMAT, $input);
	return $dateTime && $dateTime->format(OrderManager::BELGIAN_DATE_FORMAT) === $input;
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
	return null === $registryNumber->getBirthday()
		|| $registryNumber->getBirthday() === $birthDate->format('Y-m-d');
}

/**
 * Update the order meta with field value
 */
add_action('woocommerce_checkout_update_order_meta', 'dw_checkout_update_order_meta');

function dw_checkout_update_order_meta($orderId) {
	$fields = [
		OrderManager::KEY_ISSUE_DATE_DRIVING_LICENSE,
		OrderManager::KEY_DATE_OF_BIRTH,
		OrderManager::KEY_NATIONAL_REGISTRY_NUMBER,
		OrderManager::KEY_AUTOMATIC_TRANSMISSION,
		OrderManager::KEY_HAS_RECEIVED_LETTER,
	];

	foreach($fields as $field) {
		if(!empty($_POST[$field])) {
			update_post_meta($orderId, $field, dw_sanitize_text_field($field, $_POST[$field]));
		}
	}
}

function dw_sanitize_text_field($key, $value) {
	if($key === OrderManager::KEY_NATIONAL_REGISTRY_NUMBER) {
		$registryNumber = new Rijksregisternummer($value);
		$value = $registryNumber->machineFormat();
	} else {
		$value = sanitize_text_field($value);
	}

	return $value;
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
	$registryNumber = RijksregisternummerHelper::format(
		get_post_meta($order->get_id(), OrderManager::KEY_NATIONAL_REGISTRY_NUMBER, true)
	);

	echo '<p><strong>' . __('Geboortedatum') . ':</strong> <br/>'
		. get_post_meta($order->get_id(), OrderManager::KEY_DATE_OF_BIRTH, true) . '</p>';
	echo '<p><strong>' . __('Rijksregisternummer') . ':</strong> <br/>'
		. $registryNumber . '</p>';
	echo '<p><strong>' . __('Afgiftedatum rijbewijs') . ':</strong> <br/>'
		. get_post_meta($order->get_id(), OrderManager::KEY_ISSUE_DATE_DRIVING_LICENSE, true) . '</p>';
	echo '<p><strong>' . __('Automaat') . ':</strong> <br/>'
		. (get_post_meta($order->get_id(), OrderManager::KEY_AUTOMATIC_TRANSMISSION, true) === 'yes' ? __('Ja') : __('Nee')) . '</p>';
	echo '<p><strong>' . __('Brief ontvangen') . ':</strong> <br/>'
		. (get_post_meta($order->get_id(), OrderManager::KEY_HAS_RECEIVED_LETTER, true) === "yes" ? __("Ja") : __("Nee"))  . '</p>';
}

add_action('woocommerce_order_status_processing', 'dw_woocommerce_order_status_processing', 10, 1);

function dw_woocommerce_order_status_processing(int $orderId) {
	$order = new WC_Order($orderId);
	$orderManager = OrderManagerFactory::getManager();
	$orderManager->sendToDation($order);
}

add_action('woocommerce_order_actions', 'dw_order_meta_box_actions');

function dw_order_meta_box_actions(array $actions): array {
	$actions['dw_send_student_to_dashboard'] = __('Bestelling synchroniseren met Dation');

	return $actions;
}

add_action('woocommerce_order_action_dw_send_student_to_dashboard', 'dw_send_student_to_dashboard');

function dw_send_student_to_dashboard(WC_Order $order) {
	$orderManager = OrderManagerFactory::getManager();
	$orderManager->sendToDation($order);
}

add_filter('woocommerce_email_classes', 'dw_add_synchronizing_failed_email', 10, 1);

/**
 *  Add a custom email to the list of emails WooCommerce should load
 *  Add an action to trigger sending the email
 *
 * @since 0.1
 * @param array $email_classes available email classes
 * @return array filtered available email classes
 */
function dw_add_synchronizing_failed_email($email_classes) {
	if($email_classes === '') {
		$email_classes = [];
	}
	$emailClass = new EmailSyncFailed();
	// add the email class to the list of email classes that WooCommerce loads
	$email_classes['dw_failed_email'] = $emailClass;

	add_action('dw_synchronize_failed_email_action', [$emailClass, 'dw_email_trigger'], 1, 1);

	return $email_classes;

}

/**
 * @param $input
 * @return bool
 * @throws LicenseDateOverTimeException
 * @throws LicenseDateUnderTimeException
 */
function canFollowMoment($input): bool {
	$dateTime    = DateTime::createFromFormat(OrderManager::BELGIAN_DATE_FORMAT, $input);
	$dateTime->setTime(0, 0);
	$currentDate = (new DateTime())->setTime(0, 0);

	if($currentDate < $dateTime) {
		throw new LicenseDateUnderTimeException( TOO_EARLY_MESSAGE);
	}

	$diff = $dateTime->diff($currentDate);

	if($diff->y > 0 || ($diff->y === 0 && $diff->m > 11)) {
		throw new LicenseDateOverTimeException(LONG_OVERTIME_MESSAGE);
	}

	if($diff->m === 10 || $diff->m === 11) {
		throw new LicenseDateOverTimeException(OVERTIME_MESSAGE);
	}

	if($diff->m < 6) {
		throw new LicenseDateUnderTimeException(TOO_EARLY_MESSAGE);
	}

	return true;
}