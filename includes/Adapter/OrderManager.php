<?php

declare(strict_types=1);

namespace Dation\Woocommerce\Adapter;

use Dation\Woocommerce\RestApiClient\RestApiClient;

/**
 * The OrderManager is a service responsible synchronizing Woocommerce orders with Dation.
 *
 * The OrderManager ingests changes to Woocommerce orders, translating them
 * to Dation resources and synchronizing them with Dation Dashboard.
 */
class OrderManager {

	const KEY_STUDENT_ID                 = 'dw_student_id';
	const KEY_ISSUE_DATE_DRIVING_LICENSE = 'Afgiftedatum_Rijbewijs';
	const KEY_DATE_OF_BIRTH              = 'Geboortedatum';
	const KEY_NATIONAL_REGISTRY_NUMBER   = 'Rijksregisternummer';
	const KEY_AUTOMATIC_TRANSMISSION     = 'Automaat';

	/** @var RestApiClient */
    private $client;

    public function __construct(RestApiClient $client) {
        $this->client = $client;
    }

    /**
	 * Process Order
	 *
	 * This function is called when an order is set to status "Processing".
	 * This means payment has been received (paid) and stock reduced; order is
	 * awaiting fulfillment.
	 *
	 * In our context, fulfillment means synchronizing its changes to Dation
	 *
	 * @param \WC_Order $order
	 */
	public function sendToDation(\WC_Order $order) {
		global $dw_options;

		try {
			$student = $this->getStudentDataFromOrder($order);
			if(empty($student['id'])) {
				$student = $this->sendStudentToDation($student);
				update_post_meta($order->get_id(), self::KEY_STUDENT_ID, $student['id']);

				$link =  '<a target="_blank" href="https://dashboard.dation.nl/' . $dw_options['handle'] . '/leerlingen/'. $student['id'] . '">Dation</a>';

				$order->add_order_note(__("Leerling aangemaakt in $link"));
			}
		} catch (\Throwable $e) {
			do_action('woocommerce_email_classes');
			do_action('dw_action_test_email', $order);

			$note = __('Aanmaken leerling in Dation mislukt: ');
			$order->add_order_note($note . $e->getMessage());
		}
	}

	public function getStudentDataFromOrder(\WC_Order $order): array {
		$birthDate = \DateTime::createFromFormat(
			DW_BELGIAN_DATE_FORMAT,
			get_post_meta($order->get_id(), self::KEY_DATE_OF_BIRTH, true)
		);

		$issueDateDrivingLicense = \DateTime::createFromFormat(
			DW_BELGIAN_DATE_FORMAT,
			get_post_meta($order->get_id(), self::KEY_ISSUE_DATE_DRIVING_LICENSE, true)
		);
		$addressInfo = explode(' ', $order->get_billing_address_1());

		return [
			'id' => get_post_meta($order->get_id(), self::KEY_STUDENT_ID, true),
			'firstName' => $order->get_billing_first_name(),
			'lastName' => $order->get_billing_last_name(),
			'dateOfBirth' => $birthDate,
			'residentialAddress' => [
				'streetName' => $addressInfo[0],
				'houseNumber' => $addressInfo[1],//TODO: verify
				'postalCode' => $order->get_billing_postcode(),
				'city' => $order->get_billing_city(),
			],
			'emailAddress' => $order->get_billing_email(),
			'mobileNumber' => $order->get_billing_phone(),
			'nationalRegistryNumber' => get_post_meta($order->get_id(), self::KEY_NATIONAL_REGISTRY_NUMBER, true),
			'issueDate' => $issueDateDrivingLicense,
			'comments' => 'Ik rijd enkel met een automaat: ' . __(get_post_meta($order->get_id(), self::KEY_AUTOMATIC_TRANSMISSION, true))
		];
	}

	public function sendStudentToDation(array $student): array {
		return $this->client->postStudent($student);
	}
}