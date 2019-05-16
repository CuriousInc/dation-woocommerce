<?php
/**
 * Template Function Overrides
 *
 */

// Hook in
add_filter('woocommerce_checkout_fields', 'custom_override_checkout_fields');

// Our hooked in function - $fields is passed via the filter!
function custom_override_checkout_fields($fields) {

	$newOrderFields['order']['date_of_birth'] = [
		'type'     => 'text',
		'label'    => __('Geboortedatum'),
		'required' => true,
	];

	$newOrderFields['order']['rijksregisternummer'] = [
		'type'     => 'text',
		'label'    => __('Rijksregisternummer'),
		'required' => true,
	];

	$newOrderFields['order']['issue_date_driving_license'] = [
		'type'     => 'text',
		'label'    => __('Afgiftedatum rijbewijs'),
		'required' => true,
	];

	$newOrderFields['order']['automatic_gears'] = [
		'type'     => 'select',
		'options'  => [
			'yes' => __('Yes'),
			'no'  => __('No')
		],
		'label'    => __('Ik rij met automatische versnellingen'),
		'required' => true,
	];

	$fields['order'] = array_merge($newOrderFields['order'], $fields['order']);

	return $fields;
}