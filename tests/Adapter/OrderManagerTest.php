<?php

declare(strict_types=1);

namespace Dation\Tests;

use Dation\Woocommerce\Adapter\OrderManager;
use Dation\Woocommerce\PostMetaDataInterface;
use Dation\Woocommerce\RestApiClient\RestApiClient;
use Dation\WooCommerce\TranslatorInterface;
use Faker\Factory;
use Faker\Generator;
use GuzzleHttp\Client as HttpClient;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use WC_Order;

class OrderManagerTest extends TestCase {

	/** @var Generator */
	protected $faker;

	public function setUp(): void {
		$this->faker = Factory::create('nl_BE');
	}

	public function dataProvider() {
		return [
			'Use tkm' => [true],
			'Do not use tkm' => [false]
		];
	}

	/**
	 * Test parsing Order for Student information
	 *
	 * @dataProvider dataProvider
	 *
	 * @param bool $useTkm
	 */
	public function testGetStudentFromOrder(bool $useTkm): void {
		global $dw_options;
		$dw_options['use_tkm'] = $useTkm;

		// Test data
		$orderId        = $this->faker->randomNumber();
		$firstName      = $this->faker->firstName();
		$lastName       = $this->faker->lastName();
		$emailAddress   = $this->faker->safeEmail();
		$phoneNumber    = $this->faker->phoneNumber();
		$postcode       = $this->faker->postcode();
		$city           = $this->faker->city();
		$registryNumber = $this->faker->rrn();
		$dateOfBirth    = $this->faker->dateTime()->setTime(0, 0);
		$issueDate      = $this->faker->dateTime()->setTime(0, 0);
		$streetName     = $this->faker->streetName();
		$houseNumber    = $this->faker->buildingNumber();
		$isAutomatic    = $this->faker->randomElement(['yes', 'no']);
		// Mocks
		$stubApiClient = new RestApiClient(new HttpClient());

		$postMetaData = [
			$orderId => [
				'Geboortedatum'          => $dateOfBirth->format('d.m.Y'),
				'Afgiftedatum_Rijbewijs' => $issueDate->format('d.m.Y'),
				'dw_student_id'          => null,
				'Rijksregisternummer'    => $registryNumber,
				'Automaat'               => $isAutomatic
			]
		];

		/** @var TranslatorInterface $translate */
		$translate = $this->getMockBuilder(TranslatorInterface::class)
			->setMethods(['translate'])
			->getMock();
		$translate
			->method('translate')
			->willReturnCallback(function($message) {
				return $message;
			});

		/** @var PostMetaDataInterface $postMeta */
		$postMeta = $this->getMockBuilder(PostMetaDataInterface::class)
			->setMethods(['getPostMeta'])
			->getMock();

		$postMeta
			->method('getPostMeta')
			->willReturnCallback(function($orderId, $propertyName, $single) use ($postMetaData) {
				return $postMetaData[$orderId][$propertyName];
			});

		$orderManagerOptions = [
			'handle' => $this->faker->name(),
			'bankId' => $this->faker->numberBetween()
		];

		$manager = new OrderManager($stubApiClient, $orderManagerOptions, $postMeta, $translate);

		$order = $this->mockOrder([
			'get_id'                 => $orderId,
			'get_billing_address_1'  => $streetName . ' ' . $houseNumber,
			'get_billing_first_name' => $firstName,
			'get_billing_last_name'  => $lastName,
			'get_billing_postcode'   => $postcode,
			'get_billing_city'       => $city,
			'get_billing_email'      => $emailAddress,
			'get_billing_phone'      => $phoneNumber,
			'get_customer_note'      => null,
		]);

		// The actual test
		$student = $manager->getStudentFromOrder($order);

		$this->assertEquals($firstName, $student->getFirstName());
		$this->assertEquals($lastName, $student->getLastName());
		$this->assertEquals($emailAddress, $student->getEmailAddress());
		$this->assertEquals($phoneNumber, $student->getMobileNumber());
		$this->assertEquals($city, $student->getResidentialAddress()->getCity());
		$this->assertEquals($postcode, $student->getResidentialAddress()->getPostalCode());
		$this->assertEquals($streetName, $student->getResidentialAddress()->getStreetName());
		$this->assertEquals($houseNumber, $student->getResidentialAddress()->getHouseNumber());

		if($useTkm) {
			$this->assertEquals($registryNumber, $student->getNationalRegistryNumber());
			$this->assertEquals($issueDate, $student->getIssueDateCategoryBDrivingLicense());
			$this->assertEquals($dateOfBirth, $student->getDateOfBirth());

			$this->assertEquals(true, $student->isPlanAsIndependent());

			$expectedComment = 'Ik rijd enkel met een automaat: ' . ($isAutomatic === 'yes' ? 'Ja' : 'Nee');
			$this->assertEquals($expectedComment, $student->getComments());
		}
	}

	/**
	 * Mock Woocommerce order
	 *
	 * @param mixed $replacements List of function to mock [$methodName => $returnValue]
	 *
	 * @return MockObject|WC_Order
	 */
	private function mockOrder(array $replacements) {
		$orderMockBuilder = $this->getMockBuilder('WC_Order')
			->setMethods(array_keys($replacements));
		$order            = $orderMockBuilder->getMock();
		foreach ($replacements as $methodName => $returnValue) {
			$order->method($methodName)->willReturn($returnValue);
		}

		return $order;
	}
}
