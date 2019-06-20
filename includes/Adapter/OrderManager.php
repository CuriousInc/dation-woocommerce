<?php

declare(strict_types=1);

namespace Dation\Woocommerce\Adapter;

use DateTime;
use Dation\Woocommerce\RestApiClient\Model\Address;
use Dation\Woocommerce\RestApiClient\Model\Student;
use Dation\Woocommerce\RestApiClient\RestApiClient;
use Throwable;
use WC_Order;

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
	 * This function is called when an order is set to status "Processing".
	 * This means payment has been received (paid) and stock reduced; order is
	 * awaiting fulfillment.
	 *
	 * In our context, fulfillment means synchronizing its changes to Dation
	 *
	 * @param \WC_Order $order
	 */
	public function sendToDation(WC_Order $order): void {
		try {
			$student = $this->getStudentFromOrder($order);
			if(empty($student->getId())) {
				$student = $this->sendStudentToDation($student);
				update_post_meta($order->get_id(), self::KEY_STUDENT_ID, $student['id']);
				$order->add_order_note($this->syncSuccesNote($student));
			}
		} catch (Throwable $e) {
			do_action('woocommerce_email_classes');
			do_action('dw_synchronize_failed_email_action', $order);

			$note = __('Aanmaken leerling in Dation mislukt');
			$order->add_order_note("{$note}: <code>{$e->getMessage()}</code>");
		}
	}

	public function getStudentFromOrder(WC_Order $order): Student {
		$birthDate = DateTime::createFromFormat(
			DW_BELGIAN_DATE_FORMAT,
			get_post_meta($order->get_id(), self::KEY_DATE_OF_BIRTH, true)
		);

		$issueDateDrivingLicense = DateTime::createFromFormat(
			DW_BELGIAN_DATE_FORMAT,
			get_post_meta($order->get_id(), self::KEY_ISSUE_DATE_DRIVING_LICENSE, true)
		);

		$addressInfo = explode(' ', $order->get_billing_address_1());

		$student = new Student();
		$student->setId(((int)get_post_meta($order->get_id(), self::KEY_STUDENT_ID, true)) ?: null);
		$student->setFirstName($order->get_billing_first_name());
		$student->setLastName($order->get_billing_last_name());
		$student->setDateOfBirth($birthDate ?: null);
		$student->setResidentialAddress(
			(new Address())
				->setStreetName($addressInfo[0])
				->setHouseNumber($addressInfo[1])//TODO: verify
				->setPostalCode($order->get_billing_postcode())
				->setCity($order->get_billing_city())
		);
		$student->setEmail($order->get_billing_email());
		$student->setPhone($order->get_billing_phone());
		$student->setNationalRegistryNumber(
			get_post_meta($order->get_id(), self::KEY_NATIONAL_REGISTRY_NUMBER, true)
		);
		$student->setIssueDateCategoryBDrivingLicense($issueDateDrivingLicense ?: null);
		$student->setPlanAsIndependent(true);
		$student->setComments($this->getTransmissionComment($order));

		return $student;
	}

	public function sendStudentToDation(Student $student): Student {
		return $this->client->postStudent($student);
	}

	private function syncSuccesNote(Student $student): string {
		global $dw_options;

		$link = sprintf('<a target="_blank" href="%s/%s/leerlingen/%s">Dation</a>',
			DW_BASE_HOST,
			$dw_options['handle'],
			$student->getId()
		);

		return sprintf(__('Leerling aangemaakt in %s'), $link);
	}

	/**
	 * Generate comment on transmission usage
	 *
	 * @param \WC_Order $order
	 *
	 * @return string
	 */
	private function getTransmissionComment(WC_Order $order): string {
		$answer = (bool)get_post_meta($order->get_id(),
			OrderManager::KEY_AUTOMATIC_TRANSMISSION, true);

		return __('Ik rijd enkel met een automaat') . ': ' . ($answer ? __('Ja') : __('Nee'));
	}
}