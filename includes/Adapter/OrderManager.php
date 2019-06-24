<?php

declare(strict_types=1);

namespace Dation\Woocommerce\Adapter;

use DateTime;
use Dation\Woocommerce\RestApiClient\Model\Address;
use Dation\Woocommerce\RestApiClient\Model\CourseInstancePart;
use Dation\Woocommerce\RestApiClient\Model\Enrollment;
use Dation\Woocommerce\RestApiClient\Model\Student;
use Dation\Woocommerce\RestApiClient\RestApiClient;
use GuzzleHttp\Exception\ClientException;
use Throwable;
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

	public const KEY_ISSUE_DATE_DRIVING_LICENSE = 'Afgiftedatum_Rijbewijs';
	public const KEY_DATE_OF_BIRTH              = 'Geboortedatum';
	public const KEY_NATIONAL_REGISTRY_NUMBER   = 'Rijksregisternummer';
	public const KEY_AUTOMATIC_TRANSMISSION     = 'Automaat';
	public const KEY_ENROLLMENT_ID              = 'dw_has_enrollment';

	public const BELGIAN_DATE_FORMAT = 'd.m.Y';

	private const KEY_STUDENT_ID = 'dw_student_id';

	/** @var RestApiClient */
	private $client;

	/** @var string */
	private $handle;

	/** @var callable */
	private $getPostMetaCallable;

	/** @var callable */
	protected $translateCallable;

	public function __construct(
		RestApiClient $client,
		string $handle,
		callable $getPostMeta,
		callable $translateCallable
	) {
		$this->client              = $client;
		$this->handle              = $handle;
		$this->getPostMetaCallable = $getPostMeta;
		$this->translateCallable = $translateCallable;
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
			$student = $this->synchronizeStudent($order);
			$this->synchronizeEnrollment($order, $student);

		} catch (ClientException $e) {
			do_action('woocommerce_email_classes');
			do_action('dw_synchronize_failed_email_action', $order);

			$reason = json_decode($e->getResponse()->getBody()->getContents(), true);

			$note = $this->translate('Het synchroniseren met Dation is mislukt');
			$message = isset($reason['detail']) ? $reason['detail'] : $reason;

			$order->add_order_note("{$note}: <code>{$message}</code>");
		} catch (Throwable $e) {
			$note = $this->translate('Het synchroniseren met Dation is mislukt');
			$order->add_order_note("{$note}: <code>{$e->getMessage()}</code>");
		}
	}

	/**
	 * @param WC_Order $order
	 *
	 * @return Student
	 */
	private function synchronizeStudent(WC_Order $order): Student {
		$student = $this->getStudentFromOrder($order);
		if(empty($student->getId())) {
			$student = $this->sendStudentToDation($student);
			update_post_meta($order->get_id(), self::KEY_STUDENT_ID, $student->getId());
			$order->add_order_note($this->syncSuccesNote($student));
		}

		return $student;
	}

	/**
	 * @param WC_Order $order
	 * @param Student $student
	 *
	 * @return string
	 */
	private function synchronizeEnrollment(WC_Order $order, Student $student) {
		if($this->getPostMeta($order->get_id(), self::KEY_ENROLLMENT_ID, true) === '') {
			foreach ($order->get_items() as $key => $value) {
				//What if order has multiple items(products) sold?
				/** @var WC_Order_Item_Product $value */
				$product = new WC_Product($value->get_data()['product_id']);
				continue;
			}

			$courseInstanceId = (int)$product->get_sku();
			$courseInstance   = $this->client->getCourseInstance($courseInstanceId);

			$enrollment = new Enrollment();
			$slots      = [];

			foreach ($courseInstance->getParts() as $part) {
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

			update_post_meta($order->get_id(), self::KEY_ENROLLMENT_ID, true);

			$order->add_order_note(sprintf($this->translate('Leerling ingeschreven op %s'), $link));
		}
	}

	public function getStudentFromOrder(WC_Order $order): Student {
		$birthDate = DateTime::createFromFormat(
			self::BELGIAN_DATE_FORMAT,
			$this->getPostMeta($order->get_id(), self::KEY_DATE_OF_BIRTH, true)
		);

		$issueDateLicense = DateTime::createFromFormat(
			self::BELGIAN_DATE_FORMAT,
			$this->getPostMeta($order->get_id(), self::KEY_ISSUE_DATE_DRIVING_LICENSE, true)
		);

		$addressInfo = explode(' ', $order->get_billing_address_1());

		$student = new Student();
		$student->setId(((int)$this->getPostMeta($order->get_id(), self::KEY_STUDENT_ID, true)) ?: null);
		$student->setFirstName($order->get_billing_first_name());
		$student->setLastName($order->get_billing_last_name());
		$student->setDateOfBirth($birthDate ? $birthDate->setTime(0,0): null);
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
			$this->getPostMeta($order->get_id(), self::KEY_NATIONAL_REGISTRY_NUMBER, true)
		);
		$student->setIssueDateCategoryBDrivingLicense(
			$issueDateLicense ? $issueDateLicense->setTime(0,0) : null);
		$student->setPlanAsIndependent(true);
		$student->setComments($this->getTransmissionComment($order));

		return $student;
	}

	private function sendStudentToDation(Student $student): Student {
		return $this->client->postStudent($student);
	}

	private function syncSuccesNote(Student $student): string {
		$link = sprintf('<a target="_blank" href="%s/%s/leerlingen/%s">Dation</a>',
			DW_BASE_HOST,
			$this->handle,
			$student->getId()
		);

		return sprintf($this->translate('Leerling aangemaakt in %s'), $link);
	}

	/**
	 * Generate comment on transmission usage
	 *
	 * @param \WC_Order $order
	 *
	 * @return string
	 */
	private function getTransmissionComment(WC_Order $order): string {
		$answer = (bool)$this->getPostMeta($order->get_id(),
			OrderManager::KEY_AUTOMATIC_TRANSMISSION, true);

		return $this->translate('Ik rijd enkel met een automaat') . ': ' . ($answer ? $this->translate('Ja') : $this->translate('Nee'));
	}

	private function getPostMeta(int $postId, string $metaKey, bool $single) {
		return call_user_func($this->getPostMetaCallable, $postId, $metaKey, $single);
	}

	private function translate(string $string) {
		return call_user_func($this->translateCallable, $string);
	}
}