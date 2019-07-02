<?php

declare(strict_types=1);

namespace Dation\Tests;

use Dation\Woocommerce\Adapter\OrderManager;
use Dation\Woocommerce\PostMetaDataInterface;
use Dation\Woocommerce\RestApiClient\RestApiClient;
use Dation\WooCommerce\TranslatorInterface;
use Faker\Factory;
use GuzzleHttp\Client as HttpClient;
use PHPUnit\Framework\TestCase;
use WC_Order;

class OrderManagerTest extends TestCase {

	/** @var \Faker\Generator */
	protected $faker;

	public function setUp(): void {
		$this->faker = Factory::create('nl_BE');
	}

	/**
	 * Test parsing Order for Student information
	 */
	public function testGetStudentFromOrder(): void {
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

		$translate = $this->getMockBuilder(TranslatorInterface::class)
			->setMethods(['translate'])
			->getMock();
		$translate
			->method('translate')
			->willReturnCallback(function($message) {
				return $message;
			});

		$postMeta = $this->getMockBuilder(PostMetaDataInterface::class)
			->setMethods(['getPostMeta'])
			->getMock();

		$postMeta
			->method('getPostMeta')
			->willReturnCallback(function($orderId, $propertyName, $single) use ($postMetaData) {
				return $postMetaData[$orderId][$propertyName];
			});

		$manager = new OrderManager($stubApiClient, $this->faker->name(), $postMeta, $translate);

		$order = $this->mockOrder([
			'get_id'                 => $orderId,
			'get_billing_address_1'  => $streetName . ' ' . $houseNumber,
			'get_billing_first_name' => $firstName,
			'get_billing_last_name'  => $lastName,
			'get_billing_postcode'   => $postcode,
			'get_billing_city'       => $city,
			'get_billing_email'      => $emailAddress,
			'get_billing_phone'      => $phoneNumber,
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
		$this->assertEquals($registryNumber, $student->getNationalRegistryNumber());
		$this->assertEquals($issueDate, $student->getIssueDateCategoryBDrivingLicense());
		$this->assertEquals($dateOfBirth, $student->getDateOfBirth());

		$this->assertEquals(true, $student->isPlanAsIndependent());

		$expectedComment = 'Ik rijd enkel met een automaat: ' . ($isAutomatic ? 'Ja' : 'Nee');
		$this->assertEquals($expectedComment, $student->getComments());
	}

	/**
	 * Mock Wordpress' get_post_meta function
	 *
	 * @param mixed[] $metaMap
	 *
	 * @return callable
	 */
	private function mockGetPostMeta(array $metaMap): callable {
		return function (int $postId, string $metaKey, bool $single) use ($metaMap) {
			return $metaMap[$postId][$metaKey];
		};
	}

	/**
	 * Mock Woocommerce order
	 *
	 * @param mixed $replacements List of function to mock [$methodName => $returnValue]
	 *
	 * @return \PHPUnit\Framework\MockObject\MockObject|WC_Order
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