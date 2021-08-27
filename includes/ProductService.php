<?php

declare(strict_types=1);

namespace Dation\Woocommerce;

use DateTime;
use Exception;
use WC_Product;

class ProductService {

	public const DW_DEFAULT_PRODUCT_PROPERTIES = [
		'virtual'           => true,
		'manage_stock'      => true,
		'sold_individually' => true,
		'low_stock_amount'  => 0,
	];

	private $defaultCoursePrice;

	public function __construct(string $defaultCoursePrice) {
		$this->defaultCoursePrice = $defaultCoursePrice;
	}

	/**
	 * Update product setting if exists, create new product and set properties if not exists
	 *
	 * @param array $course
	 * @param WC_Product|null $woocommerceProduct
	 *
	 * @return WC_Product
	 * @throws Exception
	 */
	public function createOrUpdateWoocommerceProductFromDationCourse(array $course, WC_Product $woocommerceProduct = null) {
		$startDate          = $this->getStartDateFromData($course);
		$woocommerceProduct = $this->createOrUpdateWoocommerceProduct($course, $startDate, $woocommerceProduct);

		return $woocommerceProduct;
	}

	private function createOrUpdateWoocommerceProduct(array $course, DateTime $startDate, WC_Product $woocommerceProduct = null): WC_Product {
		if($woocommerceProduct === null) {
			$woocommerceProduct = new WC_Product();
			$woocommerceProduct->set_sku($course['id']);
			$woocommerceProduct->set_manage_stock(self::DW_DEFAULT_PRODUCT_PROPERTIES['manage_stock']);
			$woocommerceProduct->set_regular_price($this->defaultCoursePrice);
		}
		$woocommerceProduct->set_name($course['name']);
		$woocommerceProduct->set_menu_order($startDate->getTimestamp());

		$woocommerceProduct->set_description($course['name']);
		$woocommerceProduct->set_short_description($course['ccv_code'] ?? '');
		$woocommerceProduct->set_virtual(self::DW_DEFAULT_PRODUCT_PROPERTIES['virtual']);
		$woocommerceProduct->set_stock_quantity($course['remainingAttendeeCapacity']);
		$woocommerceProduct->set_sold_individually(self::DW_DEFAULT_PRODUCT_PROPERTIES['sold_individually']);
		$woocommerceProduct->set_low_stock_amount(self::DW_DEFAULT_PRODUCT_PROPERTIES['low_stock_amount']);

		$woocommerceProduct->save();

		return $woocommerceProduct;
	}

	public function setName(WC_Product $product, string $name): WC_Product {
		$product->set_name($name);
		$product->save();

		return $product;
	}

	public static function getProductAttributes(DateTime $startDate, ?string $city): array {
		return [
			'pa_datum'        => [
				'name'        => 'pa_datum',
				'value'       => $startDate->format(DUTCH_DATE),
				'position'    => 1,
				'is_visible'  => true,
				'is_taxonomy' => true,
			],
			'pa_locatie'      => [
				'name'        => 'pa_locatie',
				'value'       => $city,
				'is_visible'  => true,
				'is_taxonomy' => true,
			],
			'pa_tijd'         => [
				'name'        => 'pa_tijd',
				'value'       => $startDate->format(DUTCH_TIME),
				'is_visible'  => true,
				'is_taxonomy' => true,
			],
			'pa_slot_time'    => [
				'name'        => 'pa_slot_time',
				'is_visible'  => true,
				'is_taxonomy' => true,
			],
			'pa_pretty_date'  => [
				'name'        => 'pa_pretty_date',
				'is_visible'  => true,
				'is_taxonomy' => true,
			],
			'pa_address'      => [
				'name'        => 'pa_address',
				'is_visible'  => true,
				'is_taxonomy' => true,
			],
			'pa_ccv_code'     => [
				'name'        => 'pa_ccv_code',
				'is_visible'  => true,
				'is_taxonomy' => true,
			],
			'pa_month'        => [
				'name'        => 'pa_month',
				'is_visible'  => true,
				'is_taxonomy' => true,
			],
			'pa_product_url'  => [
				'name'        => 'pa_product_url',
				'is_visible'  => true,
				'is_taxonomy' => true,
			],
			'pa_product_name' => [
				'name'        => 'pa_product_name',
				'is_visible'  => true,
				'is_taxonomy' => true,
			]
		];
	}

	public function formatAddress(?array $address): string {
		if(null === $address) {
			return '';
		}

		return implode(', ', array_filter([
			$address['streetName'], $address['houseNumber'], $address['addition'], $address['postalCode'], $address['city']
		]));
	}

	public function getStartDateFromData(array $data): DateTime {
		return DateTime::createFromFormat(DATE_ISO8601, $data['startDate']);
	}

	public function setExternalUrlForProduct(WC_Product $woocommerceProduct, array $course, string $contactFormLocation, string $siteLocation): void {
		$courseParts = $course['parts'];

		usort($courseParts, function ($a, $b) {
			$startA = (DateTime::createFromFormat(DATE_ISO8601, $a['slots'][0]['startDate']))->getTimestamp();
			$startB = (DateTime::createFromFormat(DATE_ISO8601, $b['slots'][0]['startDate']))->getTimestamp();
			if($startA === $startB) {
				return 0;
			}

			return ($startA < $startB) ? -1 : 1;

		});

		$firstSlotStart = $courseParts[0]['slots'][0]['startDate'];
		$firstSlotEnd   = $courseParts[0]['slots'][0]['endDate'];

		$trainingId   = $woocommerceProduct->get_sku();
		$location     = isset($courseParts[0]['slots'][0]['location']['address']['city']) ? $courseParts[0]['slots'][0]['location']['address']['city'] : '';
		$trainingName = $course['name'];

		$startDate  = DateTime::createFromFormat(DATE_ISO8601, $firstSlotStart);
		$endDate    = DateTime::createFromFormat(DATE_ISO8601, $firstSlotEnd);
		$dateString = urlencode($startDate->format('d-m-Y H:i') . '-' . $endDate->format('H:i'));

		$url = "$siteLocation/$contactFormLocation?dw_trainingId=$trainingId&dw_location=$location&dw_trainingName=$trainingName&dw_start_date=$dateString";

		$woocommerceProduct->update_meta_data('product_url', $url);

		$woocommerceProduct->save();
	}
}
