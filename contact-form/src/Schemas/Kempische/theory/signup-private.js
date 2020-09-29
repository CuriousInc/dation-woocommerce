import React from "react";
import { submitFunction } from "../../Default/signup-company";
import DateInput from "../../../Widgets/DateInput";

const CAT_B = 'kempische_b';
const CAT_G = 'kempische_g';
const CAT_BE = 'kempische_be';

function getStudentProperties(type) {
	return {
		firstName: {
			type: 'string',
			title: 'Voornaam',
			default: '',
		},
		lastName: {
			type: 'string',
			title: 'Naam',
		},
		street: {
			type: 'string',
			title: 'Straat',
		},
		houseNumber: {
			type: 'string',
			title: 'Huisnummer',
		},
		zipCode: {
			type: 'string',
			title: 'Postcode',
		},
		city: {
			type: 'string',
			title: 'Plaats',
		},
		mobileNumber: {
			type: 'string',
			title: 'Mobiele nummer',
			minLength: 10,
		},
		phoneNumber: {
			type: 'string',
			title: 'Telefoonnummer',
			minLength: 10,
		},
		emailAddress: {
			type: 'string',
			format: 'email',
			title: 'E-mailadres (Gelieve per persoon een uniek e-mailadres in te voeren)',
		},
		birthPlace: {
			type: 'string',
			title: 'Geboorteplaats',
		},
		birthDate: {
			type: 'string',
			title: 'Geboortedatum',
		},
		nationalRegistryNumber: {
			type: 'string',
			title: 'Rijksregisternummer (Achterzijde EID)'
		},
		identityCardNumber: {
			type: 'string',
			title: 'Identiteitskaartnummer (Voorzijde EID)',
		},
		location: {
			type: 'string',
			title: 'Locatie',
			enum: getLocations(type),
		},
		...getExtraFields(type),
		availability: {
			type: 'string',
			title: 'Wanneer kan u zich vrijmaken om de lessen te volgen?'
		},
		privacy: {
			type: 'boolean',
			title: 'Akkoord met de privacyverklaring en algemene voorwaarden.',
		},
		education: {
			type: 'string',
		},
		packageName: {
			type: 'string'
		}
	}
}

function getLocations(type) {
	switch(type) {
		case CAT_B:
			return ['Herenthout', 'Westerlo', 'Booischot', 'Herentals', 'Nijlen', 'Heist op den Berg', 'Putte', 'Scherpenheuvel', 'Ramsel', 'Berlaar', 'Tremelo', 'Zandhoven', 'Tienen'];
		case CAT_G:
		case CAT_BE:
			return ['Herenthout'];
	}
}

export function getStudentUiSchema() {
	return {
		firstName: {
			classNames: 'form-input-sm col-xs-12 col-sm-6',
		},
		lastName: {
			classNames: 'form-input-sm col-xs-12 col-sm-6',
		},
		zipCode: {
			classNames: 'form-input-sm col-xs-12 col-sm-6',
		},
		houseNumber: {
			classNames: 'form-input-sm col-xs-12 col-sm-6',
		},
		street: {
			classNames: 'form-input-sm col-xs-12 col-sm-6',
		},
		city: {
			classNames: 'form-input-sm col-xs-12 col-sm-6',
		},
		mobileNumber: {
			classNames: 'form-input-sm col-xs-12 col-sm-6',
		},
		phoneNumber: {
			classNames: 'form-input-sm col-xs-12 col-sm-6',
		},
		emailAddress: {
			classNames: 'form-input-sm col-xs-12',
		},
		birthPlace: {
			classNames: 'form-input-sm col-xs-12 col-sm-6',
		},
		birthDate: {
			classNames: 'form-input-sm col-xs-12 col-sm-6',
			'ui:widget': (props) => <DateInput {...props} />,
			'ui:options': {
				timeFormat: false,
			},
		},
		dateTheoryExamPassed: {
			classNames: 'form-input-sm col-xs-12 col-sm-6',
			'ui:widget': (props) => <DateInput {...props} />,
			'ui:options': {
				timeFormat: false,
			},
		},
		startDateProvisionalLicence: {
			classNames: 'form-input-sm col-xs-12 col-sm-6',
			'ui:widget': (props) => <DateInput {...props} />,
			'ui:options': {
				timeFormat: false,
			},
		},
		endDateProvisionalLicence: {
			classNames: 'form-input-sm col-xs-12 col-sm-6',
			'ui:widget': (props) => <DateInput {...props} />,
			'ui:options': {
				timeFormat: false,
			},
		},
		dateBLicencePassed: {
			classNames: 'form-input-sm col-xs-12 col-sm-6',
			'ui:widget': (props) => <DateInput {...props} />,
			'ui:options': {
				timeFormat: false,
			},
		},
		nationalRegistryNumber: {
			classNames: 'form-input-sm col-xs-6',
		},
		identityCardNumber: {
			classNames: 'form-input-sm col-xs-6',
		},
		packageName: {
			'ui:widget': 'hidden',
		},
		education: {
			'ui:widget': 'hidden',
		},
		privacy: {
			classNames: 'col-xs-12',
		},
		location: {
			classNames: 'form-input-sm col-xs-12'
		},
		availability: {
			'ui:widget': 'textarea',
			classNames: 'form-input-sm col-xs-12'
		}
	}
}

function getExtraFields(type) {
	switch(type) {
		case CAT_B:
			return {
				dateTheoryExamPassed: {
					type: 'string',
					title: 'Datum geslaagd theorie (achterzijde VLR of attest geslaagd)',
				},
				startDateProvisionalLicence: {
					type: 'string',
					title: 'Begindatum voorlopig rijbewijs (4a)',
				},
				endDateProvisionalLicence: {
					type: 'string',
					title: 'Einddatum voorlopig rijbewijs (4b)',
				},
			}
		case CAT_G: {
			return {
				dateTheoryExamPassed: {
					type: 'string',
					title: 'Datum geslaagd theorie (attest geslaagd)',
				},
			}
		}
		case CAT_BE: {
			return {
				dateBLicencePassed: {
					type: 'string',
					title: 'Datum rijbewijs B behaald'
				}
			}
		}
	}
}

function getRequiredFields(type) {
	const defaultRequired = ['firstName', 'lastName', 'street', 'houseNumber', 'zipCode', 'city', 'mobileNumber', 'emailAddress', 'birthPlace', 'birthDate', 'identityCardNumber', 'nationalRegistryNumber', 'privacy', 'location']
	switch(type) {
		case CAT_B:
		case CAT_G:
			return defaultRequired;
		case CAT_BE:
			return [...defaultRequired, ['dateBLicencePassed']];
	}
}

export function getStudentDefinition(type) {
	return {
		type: 'object',
		title: 'Kandidaat',
		required: getRequiredFields(type),
		properties: getStudentProperties(type),
	}
}

export default function(type) {
	return {
		onSubmit: async ({ formData }) => {
			submitFunction(formData, 'lead');
		},
		schema: getStudentDefinition(type),
		uiSchema: getStudentUiSchema()
	}
}

