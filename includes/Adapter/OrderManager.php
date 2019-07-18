<?php

declare(strict_types=1);

namespace Dation\Woocommerce\Adapter;

use DateTime;
use Dation\Woocommerce\Exceptions\LicenseDateOverTimeException;
use Dation\Woocommerce\Exceptions\LicenseDateUnderTimeException;
use Dation\Woocommerce\Model\Address;
use Dation\Woocommerce\Model\CourseInstancePart;
use Dation\Woocommerce\Model\Enrollment;
use Dation\Woocommerce\Model\Invoice;
use Dation\Woocommerce\Model\Payment;
use Dation\Woocommerce\Model\PaymentParty;
use Dation\Woocommerce\Model\Student;
use Dation\Woocommerce\PostMetaDataInterface;
use Dation\Woocommerce\RestApiClient\RestApiClient;
use Dation\Woocommerce\TranslatorInterface;
use GuzzleHttp\Exception\ClientException;
use Throwable;
use VIISON\AddressSplitter\AddressSplitter;
use VIISON\AddressSplitter\Exceptions\SplittingException;
use WC_Order;
use WC_Order_Item_Product;
use WC_Product;

/**
 * The OrderManager is a service responsible synchronizing Woocommerce orders with Dation.
 *
 * The OrderManager ingests changes to Woocommerce orders, translating them
 * to Dation resources and synchronizing them with Dation Dashboard.
 */
class OrderManager {

	const KEY_ISSUE_DATE_DRIVING_LICENSE = 'Afgiftedatum_Rijbewijs';
	const KEY_DATE_OF_BIRTH              = 'Geboortedatum';
	const KEY_NATIONAL_REGISTRY_NUMBER   = 'Rijksregisternummer';
	const KEY_AUTOMATIC_TRANSMISSION     = 'Automaat';
	const KEY_HAS_RECEIVED_LETTER        = 'dw_has_received_letter';
	const KEY_ENROLLMENT_ID              = 'dw_has_enrollment';
	const KEY_PAYMENT_ID                 = 'dw_has_payment';
	const KEY_INVOICE_ID                 = 'dw_has_invoice';
	const KEY_STUDENT_ID                 = 'dw_student_id';

	const BELGIAN_DATE_FORMAT = 'd.m.Y';

	/** @var RestApiClient */
	private $client;

	/** @var string */
	private $handle;

	/** @var PostMetaDataInterface */
	private $postMetaData;

	/** @var TranslatorInterface */
	protected $translator;

	/** @var int */
	protected $bankId;

	public function __construct(
		RestApiClient $client,
		array $options,
		PostMetaDataInterface $postMetaData,
		TranslatorInterface $translator
	) {
		$this->client              = $client;
		$this->handle              = $options['handle'];
		$this->bankId              = $options['bankId'];
		$this->postMetaData        = $postMetaData;
		$this->translator          = $translator;
	}

	/**
	 * This function is called when an order is set to status "Processing".
	 * This means payment has been received (paid) and stock reduced; order is
	 * awaiting fulfillment.
	 *
	 * In our context, fulfillment means synchronizing
	 * - the student,
	 * - the payment,
	 * - the enrollment
	 * to Dation.
	 *
	 * Next steps are generating an invoice for the enrollment and linking the payment to that invoice.
	 *
	 * @param \WC_Order $order
	 */
	public function sendToDation(WC_Order $order): void {
		try {
			$student = $this->synchronizeStudentToDation($order);

			$this->synchronizeEnrollmentToDation($order, $student);

			$this->billEnrollment($order);

			$this->synchronizePaymentToDation($student, $order);

			$this->sendWarningEmail($order);

		} catch(Throwable $e) {
			$this->coughtErrorActions($order,'Synchronisatie mislukt', $e->getMessage());
		}
	}

	/**
	 * Actions to be taken when one of the steps in synchronizing the order to Dation fails.
	 * Sends an email to a specified wordpress user and generates a failure note on the order.
	 *
	 * @param WC_Order $order
	 * @param string $errorType
	 * @param $message
	 */
	private function coughtErrorActions(WC_Order $order, string $errorType, $message):void {
		do_action('woocommerce_email_classes');
		do_action('dw_synchronize_failed_email_action', $order);

		$note = $this->translator->translate($errorType);
		$order->add_order_note("{$note}: <code>{$message}</code>");
	}

	private function synchronizeStudentToDation(WC_Order $order): Student {
		try {
			$student = $this->getStudentFromOrder($order);
			if(empty($student->getId())) {
				$student = $this->sendStudentToDation($student);
				update_post_meta($order->get_id(), self::KEY_STUDENT_ID, $student->getId());
				$order->add_order_note($this->syncSuccessNote($student));
			}

			return $student;
		} catch (ClientException $e) {
			$reason = json_decode($e->getResponse()->getBody()->getContents(), true);
			$message = isset($reason['detail']) ? $reason['detail'] : $reason;

			$this->coughtErrorActions($order,'Het synchroniseren van de student is mislukt', $message);
		}

	}

	private function synchronizeEnrollmentToDation(WC_Order $order, Student $student):void {
		try {
			if($this->postMetaData->getPostMeta($order->get_id(), self::KEY_ENROLLMENT_ID, true) === '') {
				foreach($order->get_items() as $key => $value) {
					//What if order has multiple items(products) sold?
					/** @var WC_Order_Item_Product $value */
					$product = new WC_Product($value->get_data()['product_id']);
					continue;
				}

				$courseInstanceId = (int)$product->get_sku();
				$courseInstance   = $this->client->getCourseInstance($courseInstanceId);

				$enrollment = new Enrollment();
				$slots      = [];

				foreach($courseInstance->getParts() as $part) {
					//What if a part has more slots?
					/** @var CourseInstancePart $part */
					$slots[] = $part->getSlots()[0];
				}

				$enrollment->setSlots($slots);
				$enrollment->setStudent($student);

				/** @var Enrollment $synchedEnrollment */
				$synchedEnrollment = $this->client->postEnrollment($courseInstanceId, $enrollment);

				$link = sprintf('<a target="_blank" href="%s/%s/nascholing/details?id=%s">Training</a>',
					DW_BASE_HOST,
					$this->handle,
					$courseInstanceId
				);

				update_post_meta($order->get_id(), self::KEY_ENROLLMENT_ID, $synchedEnrollment->getId());

				$order->add_order_note(sprintf($this->translator->translate('Leerling ingeschreven op %s'), $link));
			}
		} catch(ClientException $e) {
			if($e->hasResponse() && $e->getResponse()->getStatusCode() == 404) {
				$message = 'Cursus niet gevonden';
			}
			$reason = json_decode($e->getResponse()->getBody()->getContents(), true);
			$message = isset($reason['detail']) ? $reason['detail'] : $reason;

			$this->coughtErrorActions($order, 'Het synchroniseren van de inschrijving is mislukt', $message);
		}
	}

	/**
	 * Create a Student object from Woocommerce order data
	 *
	 * @param WC_Order $order
	 *
	 * @return Student
	 */
	public function getStudentFromOrder(WC_Order $order): Student {
		$birthDate = DateTime::createFromFormat(
			self::BELGIAN_DATE_FORMAT,
			$this->postMetaData->getPostMeta($order->get_id(), self::KEY_DATE_OF_BIRTH, true)
		);

		$issueDateLicense = DateTime::createFromFormat(
			self::BELGIAN_DATE_FORMAT,
			$this->postMetaData->getPostMeta($order->get_id(), self::KEY_ISSUE_DATE_DRIVING_LICENSE, true)
		);

		$student = new Student();
		$student->setId(
			(int)$this->postMetaData->getPostMeta($order->get_id(), self::KEY_STUDENT_ID, true)
				?: null);
		$student->setFirstName($order->get_billing_first_name());
		$student->setLastName($order->get_billing_last_name());
		$student->setDateOfBirth($birthDate ? $birthDate->setTime(0,0): null);
		$student->setResidentialAddress($this->getAddressFromOrder($order));
		$student->setEmailAddress($order->get_billing_email());
		$student->setMobileNumber($order->get_billing_phone());
		$student->setNationalRegistryNumber(
			$this->postMetaData->getPostMeta($order->get_id(), self::KEY_NATIONAL_REGISTRY_NUMBER, true)
		);
		$student->setIssueDateCategoryBDrivingLicense(
			$issueDateLicense ? $issueDateLicense->setTime(0,0) : null);
		$student->setPlanAsIndependent(true);
		$student->setComments($this->getTransmissionComment($order));

		return $student;
	}

	private function getAddressFromOrder(WC_Order $order): Address {
		try {
			$address = AddressSplitter::splitAddress($order->get_billing_address_1());
		} catch(SplittingException $e) {
			//Add note, don't stop functionality
			$order->add_order_note('Let op! Er is iets misgegaan bij het synchroniseren van het adres van de leerling');
		}

		$streetName           = $address['streetName'];
		$houseNumberExtension = empty($address['extension']) ? '' : $address['extension'];
		$houseNumber          = $address['houseNumber'] . $houseNumberExtension;

		return (new Address())
			->setStreetName($streetName)
			->setHouseNumber($houseNumber)
			->setPostalCode($order->get_billing_postcode())
			->setCity($order->get_billing_city());
	}

	private function sendStudentToDation(Student $student): Student {
		return $this->client->postStudent($student);
	}

	private function syncSuccessNote(Student $student): string {
		$link = sprintf('<a target="_blank" href="%s/%s/leerlingen/%s">Dation</a>',
			DW_BASE_HOST,
			$this->handle,
			$student->getId()
		);

		return sprintf($this->translator->translate('Leerling aangemaakt in %s'), $link);
	}

	/**
	 * Generate comment on usage of manual/automatic car transmission
	 *
	 * @param \WC_Order $order
	 *
	 * @return string
	 */
	private function getTransmissionComment(WC_Order $order): string {
		$answer = $this->postMetaData->getPostMeta($order->get_id(),
				OrderManager::KEY_AUTOMATIC_TRANSMISSION, true) === 'yes';

		return $this->translator->translate('Ik rijd enkel met een automaat')
			. ': '
			. ($answer ? $this->translator->translate('Ja') : $this->translator->translate('Nee'));
	}

	private function synchronizePaymentToDation(Student $student, WC_Order $order) {
		$paymentId = $this->postMetaData->getPostMeta($order->get_id(), self::KEY_PAYMENT_ID, true);
		$invoiceId = $this->postMetaData->getPostMeta($order->get_id(), self::KEY_INVOICE_ID, true);
		try {
			if($paymentId === ''
				&& $invoiceId !== ''
				&& !empty($student->getId())
			) {
				$payment = new Payment();

				$invoice = (new Invoice())->setId((int)$invoiceId);

				$studentParty = (new PaymentParty())
					->setType(PaymentParty::TYPE_STUDENT)
					->setId($student->getId());

				$bankParty = (new PaymentParty())
					->setType(PaymentParty::TYPE_BANK)
					->setId($this->bankId);

				$payment
					->setPayer($studentParty)
					->setPayee($bankParty)
					->setInvoice($invoice)
					->setAmount(floatval($order->get_total()))
					->setDescription('Betaald via websitekoppeling');

				$this->client->postPayment($payment);

				update_post_meta($order->get_id(), self::KEY_PAYMENT_ID, true);

				$order->add_order_note($this->translator->translate('Betaling toegevoegd'));
			}
		} catch(ClientException $e) {
			$reason = json_decode($e->getResponse()->getBody()->getContents(), true);
			$message = isset($reason['detail']) ? $reason['detail'] : $reason;

			$this->coughtErrorActions($order, 'Het synchroniseren van de betaling is mislukt', $message);
		}
	}

	private function billEnrollment(WC_Order $order): void {
		$invoiceId = $this->postMetaData->getPostMeta($order->get_id(), self::KEY_INVOICE_ID, true);
		$enrollmentId = $this->postMetaData->getPostMeta($order->get_id(), self::KEY_ENROLLMENT_ID, true);
		try {
			if(
				$invoiceId == ''
				&& $enrollmentId !== ''
			) {
				$enrollment = (new Enrollment())->setId((int)$enrollmentId);
				/** @var Invoice $invoice */
				$invoice = $this->client->billEnrollment($enrollment)[0];

				update_post_meta($order->get_id(), self::KEY_INVOICE_ID, $invoice->getId());

				$order->add_order_note($this->translator->translate('Inschrijving gefactureerd'));
			}
		} catch (ClientException $e) {
			$reason = json_decode($e->getResponse()->getBody()->getContents(), true);
			$message = isset($reason['detail']) ? $reason['detail'] : $reason;

			$this->coughtErrorActions($order, 'Het factureren van de inschrijving is mislukt', $message);
		}
	}

	private function sendWarningEmail(WC_Order $order) {
		$hasReceivedLetter = $this->postMetaData->getPostMeta($order->get_id(), self::KEY_HAS_RECEIVED_LETTER, true);
		$issueDateDrivingLicense = $this->postMetaData->getPostMeta($order->get_id(), self::KEY_ISSUE_DATE_DRIVING_LICENSE, true);

		if($hasReceivedLetter === 'no' || !$this->orderManagerCanFollowMoment($issueDateDrivingLicense)) {
			do_action('woocommerce_email_classes');
			do_action('dw_warning_email_action', $order);
		}
	}

	private function orderManagerCanFollowMoment($issueDateDrivingLicense) {
		try {
			return canFollowMoment($issueDateDrivingLicense);
		} catch (LicenseDateOverTimeException $e) {
			return false;
		} catch (LicenseDateUnderTimeException $e) {
			return false;
		}
	}
}