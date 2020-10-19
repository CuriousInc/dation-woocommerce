<?php

use Dation\Woocommerce\ProductService;
use PHPUnit\Framework\TestCase;

class ProductServiceTest extends TestCase {
	/** @var \Faker\Generator */
	protected $faker;

	public function setUp(): void {
		$this->faker = Faker\Factory::create();
	}

	public function testCreateOrUpdateWoocommerceProductFromDationCourse() {
		$dateTime       = $this->faker->dateTime;
		$course         = $this->getCourseDataArray($dateTime);
		$regularPrice   = $this->faker->numberBetween();
		$productService = new ProductService((string)$regularPrice);

		$product = $this->getMockBuilder(WC_Product::class)
			->setMethods([
				'set_name',
				'set_menu_order',
				'set_description',
				'set_short_description',
				'set_regular_price',
				'set_virtual',
				'set_stock_quantity',
				'set_sold_individually',
				'set_low_stock_amount',
				'save'
			])
			->getMock();

		$product->expects(self::once())
			->method('set_name')
			->with($course['name']);

		$product->expects(self::once())
			->method('set_menu_order')
			->with($dateTime->getTimestamp());

		$product->expects(self::once())
			->method('set_description')
			->with($course['name']);

		$product->expects(self::once())
			->method('set_short_description')
			->with($course['ccv_code']);

		$product->expects(self::once())
			->method('set_regular_price')
			->with($regularPrice);

		$product->expects(self::once())
			->method('set_virtual')
			->with(ProductService::DW_DEFAULT_PRODUCT_PROPERTIES['virtual']);

		$product->expects(self::once())
			->method('set_stock_quantity')
			->with($course['remainingAttendeeCapacity']);

		$product->expects(self::once())
			->method('set_sold_individually')
			->with(ProductService::DW_DEFAULT_PRODUCT_PROPERTIES['sold_individually']);

		$product->expects(self::once())
			->method('set_low_stock_amount')
			->with(ProductService::DW_DEFAULT_PRODUCT_PROPERTIES['low_stock_amount']);

		$product->expects(self::once())
			->method('save');

		$productService->createOrUpdateWoocommerceProductFromDationCourse($course, $product);
	}

	public function testSetExternalUrl() {
		$course  = $this->getCourseDataArray(new DateTime());
		$product = $this->getMockBuilder(WC_Product::class)
			->setMethods(['get_sku', 'update_meta_data', 'save'])
			->getMock();

		$sku = 1;

		$product
			->method('get_sku')
			->willReturn($sku);

		$product->expects($this->once())
			->method('save');

		$product->expects($this->once())
			->method('update_meta_data')
			->with('product_url');

		$productService = new ProductService('0');
		$productService->setExternalUrlForProduct($product, $course, '', '');
	}

	private function getCourseDataArray(DateTime $startDateSlot1): array {
		$endDateSlot1   = (clone $startDateSlot1)->modify('+2 hours');

		$startDateSlot2 = new DateTime('+1 weeks');
		$endDateSlot2   = (clone $startDateSlot2)->modify('+1 hour');

		return [
			'name'                      => '',
			'remainingAttendeeCapacity' => $this->faker->numberBetween(0, 20),
			'ccv_code'                   => $this->faker->name,
			'startDate'                 => $startDateSlot1->format(DATE_ISO8601),
			'parts'                     => [
				[
					'slots' => [
						[
							'startDate' => $startDateSlot1->format(DATE_ISO8601),
							'endDate'   => $endDateSlot1->format(DATE_ISO8601),
							'location'  => [
								'address' => [
									'streetName'  => '',
									'houseNumber' => '',
									'addition'    => '',
									'postalCode'  => '',
									'city'        => ''
								]
							]
						]
					]
				],
				[
					'slots' => [
						[
							'startDate' => $startDateSlot2->format(DATE_ISO8601),
							'endDate'   => $endDateSlot2->format(DATE_ISO8601),
							'location'  => [
								'address' => [
									'streetName'  => '',
									'houseNumber' => '',
									'addition'    => '',
									'postalCode'  => '',
									'city'        => ''
								]
							]
						],
					]
				]
			]
		];
	}
}
