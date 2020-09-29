import { submitFunction } from "../Default/signup-company";
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
		address: {
			type: 'string',
			title: 'Adres'
		},
		zipCode: {
			type: 'string',
			title: 'Postcode'
		},
		city: {
			type: 'string',
			title: 'Plaats'
		},
		email: {
			type: 'string',
			format: 'email',
			title: 'E-mailadres',
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
		address: {
			classNames: 'form-input-sm col-xs-12 col-sm-6',
		},
		zipCode: {
			classNames: 'form-input-sm col-xs-12 col-sm-6',
		},
		city: {
			classNames: 'form-input-sm col-xs-12 col-sm-6',
		},
		email: {
			classNames: 'form-input-sm col-xs-12 col-sm-6',
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
		required: ['companyName', 'contactPerson', 'address', 'zipCode', 'city', 'privacy', 'email'],
		properties: getCompanyProperties()
	}
}

export default {
		onSubmit: async ({ formData }) => {
			submitFunction(formData, 'companyLead');
		},
		schema: {
			definitions: {
				student: getStudentDefinition(false),
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
