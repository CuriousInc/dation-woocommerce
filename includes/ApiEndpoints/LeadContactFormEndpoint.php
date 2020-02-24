<?php
declare(strict_types=1);

namespace Dation\Woocommerce\ApiEndpoints;


use Dation\Woocommerce\Adapter\RestApiClientFactory;
use Dation\Woocommerce\RestApiClient\RestApiClient;
use GuzzleHttp\Exception\ClientException;
use WP_REST_Server;

class LeadContactFormEndpoint extends \WP_REST_Controller {
	/** @var RestApiClient */
	private $apiClient;

	public function __construct() {
		$this->apiClient = RestApiClientFactory::getClient();
	}

	private const NOTES_KEY           = 'notes';
	private const PARAMETER_WHITELIST = [
		'firstName',
		'lastName',
		'instertion',
		'gender',
		'birthDate',
		'birthPlace',
		'zipCode',
		'street',
		'houseNumber',
		'city',
		'emailOptIn',
		'emailAddress',
		'phoneNumber',
		'mobileNumber',
		'startDate',
		'formId',
		'campaignSourceId',
		'lessons',
		'category',
		'notes',
		'nationalRegistryNumber',
		'idCardNumber',
	];

	public function register_routes() {
		$version   = '1';
		$namespace = 'dationwoocommerce/v' . $version;
		$base      = '/submit';

		register_rest_route($namespace, '/' . $base . '/lead', [
			'methods'  => WP_REST_Server::CREATABLE,
			'callback' => [$this, 'submitLeadForm'],
		]);

		register_rest_route($namespace, '/' . $base . '/companyLead', [
			'methods'  => WP_REST_Server::CREATABLE,
			'callback' => [$this, 'submitCompanyLeadForm']
		]);
	}

	public function submitLeadForm(\WP_REST_Request $request) {
		$requestParameters = $request->get_json_params();
		$formData          = $this->getLeadFromPostDate($requestParameters);

		try {
			$response = $this->apiClient->postLead($formData)->getBody()->getContents();
//			TODO return success/error
		} catch(ClientException $e) {

		}

	}

	public function submitCompanyLeadForm( $request) {
		$requestParameters = $request->get_json_params();
		$leads             = [];

		$companyData = $this->getCompanyData($requestParameters['company']);
		foreach($requestParameters['leads'] as $lead) {
			$lead = $this->getLeadFromPostDate($lead);
			$lead['notes'] = isset($lead['notes']) ? $lead['notes'] . $companyData : $companyData;

			$leads[] = $lead;
		}

		$responses = [];
		foreach($leads as $lead) {
			$response = $this->apiClient->postLead($lead);
			$responses[] = $response->getBody()->getContents();
		}


	}

	private function getLeadFromPostDate($leadArray = []) {
		$nonDefaultParams = [];
		$formData         = [];
		foreach($leadArray as $key => $value) {
			if(in_array($key, self::PARAMETER_WHITELIST)) {
				$formData[$key] = $value;
			} else {
				$nonDefaultParams[$key] = $value;
			}
		}
		$notes = isset($formData[self::NOTES_KEY]) ? $formData[self::NOTES_KEY] : '';
		// We need two loops to make sure we do not override the manually filled in notes
		foreach($nonDefaultParams as $key => $value) {
			$extraInformation = " ||| {$key}: {$value}";
			$notes            .= $extraInformation;
		}

		$formData[self::NOTES_KEY] = $notes;

		return $formData;
	}

	private function getCompanyData($companyData = []) {
		$notes = ' BedrijfsInformatie: ';
		foreach($companyData as $key => $value) {
			$extraInformation = " {$key}: {$value} |||";
			$notes            .= $extraInformation;
		}

		return $notes;

	}
}