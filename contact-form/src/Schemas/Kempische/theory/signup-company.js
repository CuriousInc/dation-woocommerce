import { submitFunction } from "../../Default/signup-company";
import { getStudentDefinition, getStudentUiSchema } from "./signup-private";

function getCompanyProperties() {
	return {
		companyName: {
			type: 'string',
			title: 'Bedrijfsnaam',
		},
		contactPerson: {
			type: 'string',
			title: 'Contactpersoon',
		},
		street: {
			type: 'string',
			title: 'Straat'
		},
		houseNumber: {
			type: 'string',
			title: 'Huisnummer'
		},
		zipCode: {
			type: 'string',
			title: 'Postcode'
		},
		city: {
			type: 'string',
			title: 'Plaats'
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
		VATRegistration: {
			type: 'string',
			title: 'BTW-nummer',
		},
		email: {
			type: 'string',
			format: 'email',
			title: 'E-mailadres',
		},
		emailInvoice: {
			type: 'string',
			format: 'email',
			title: 'E-mailadres facturatie',
		},
		financedThroughSubsidy: {
			type: 'boolean',
			title: 'Financiering via subsidie',
		},
		privacy: {
			type: 'boolean',
			title: 'Akkoord met de privacyverklaring en algemene voorwaarden.',
		},
	};
}

function getCompanyUiSchema() {
	return {
		companyName: {
			classNames: 'form-input-sm col-xs-12 col-sm-6',
		},
		contactPerson: {
			classNames: 'form-input-sm col-xs-12 col-sm-6',
		},
		street: {
			classNames: 'form-input-sm col-xs-12 col-sm-6',
		},
		houseNumber: {
			classNames: 'form-input-sm col-xs-12 col-sm-6',
		},
		zipCode: {
			classNames: 'form-input-sm col-xs-12 col-sm-6',
		},
		city: {
			classNames: 'form-input-sm col-xs-12 col-sm-6',
		},
		VATRegistration: {
			classNames: 'form-input-sm col-xs-12 col-sm-6',
		},
		mobileNumber: {
			classNames: 'form-input-sm col-xs-12 col-sm-6',
		},
		phoneNumber: {
			classNames: 'form-input-sm col-xs-12 col-sm-6',
		},
		email: {
			classNames: 'form-input-sm col-xs-12 col-sm-6',
		},
		emailInvoice: {
			classNames: 'form-input-sm col-xs-12 col-sm-6'
		},
		financedThroughSubsidy: {
			classNames: 'col-xs-12'
		},
		privacy: {
			classNames: 'col-xs-12'
		}
	};
}

function getCompanyDefinition() {
	return {
		type: 'object',
		title: 'Bedrijven',
		required: ['companyName', 'street', 'houseNumber', 'zipCode', 'city', 'VATRegistration', 'privacy', 'email'],
		properties: getCompanyProperties()
	}
}

export default function(type) {
	return {
		onSubmit: async ({ formData }) => {
			submitFunction(formData, 'companyLead');
		},
		schema: {
			definitions: {
				student: getStudentDefinition(type, true),
				company: getCompanyDefinition(),
			},
			required: [],
			properties: {
				company: {
					type: 'object',
					title: 'Bedrijf',
					$ref: '#/definitions/company',
				},
				students: {
					type: 'array',
					title: '',
					minItems: 1,
					items: {
						$ref: '#/definitions/student'
					}
				}
			}
		},
		uiSchema: {
			company: getCompanyUiSchema(),
			students: {
				items: getStudentUiSchema(),
				'ui:options': {
					orderable: false,
				}
			}
		}
	}
}
