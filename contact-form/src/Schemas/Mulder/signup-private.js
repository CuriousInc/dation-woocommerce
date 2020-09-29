import React from "react";
import {submitFunction} from "../Default/signup-company";
import DateInput from "../../Widgets/DateInput";


function getStudentProperties(extended) {
	const extendedFields = extended ? {
		zipCode: {
			type: 'string',
			title: 'Postcode',
		},
		houseNumber: {
			type: 'string',
			title: 'Huisnummer',
		},
		street: {
			type: 'string',
			title: 'Straat',
		},
		city: {
			type: 'string',
			title: 'Plaats',
		},
		phoneNumber: {
			type: 'string',
			title: 'Telefoonnummer',
			minLength: 10,
		},
	} : {};
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
		emailAddress: {
			type: 'string',
			format: 'email',
			title: 'E-mailadres',
		},
		...extendedFields,
		birthDate: {
			type: 'string',
			title: 'Geboortedatum',
		},
		code95EndDate: {
			type: 'string',
			title: 'Einddatum Code95',
		},
		privacy: {
			type: 'boolean',
			title: 'Akkoord met de privacyverklaring en algemene voorwaarden.',
		},
		trainingId: {
			type: 'string',
		},
		titel: {
			type: 'string',
		},
		datum: {
			type: 'string',
		},
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
		phoneNumber: {
			classNames: 'form-input-sm col-xs-12 col-sm-6',
		},
		emailAddress: {
			classNames: 'form-input-sm col-xs-12',
		},
		birthDate: {
			classNames: 'form-input-sm col-xs-12 col-sm-6',
			'ui:widget': (props) => <DateInput {...props} />,
			'ui:options': {
				timeFormat: false,
			},
		},
		privacy: {
			classNames: 'col-xs-12',
		},
		code95EndDate: {
			classNames: 'form-input-sm col-xs-12 col-sm-6',
			'ui:widget': (props) => <DateInput {...props} />,
			'ui:options': {
				timeFormat: false,
			},
		},
		trainingId: {
			'ui:widget': 'hidden',
		},
		titel: {
			'ui:widget': 'hidden',
		},
		datum: {
			'ui:widget': 'hidden',
		},
	}
}

export function getStudentDefinition(extended = true) {
	const extendedRequired = extended ? ['zipCode', 'houseNumber', 'city', 'street', 'phoneNumber'] : [];
	return {
		type: 'object',
		title: 'Kandidaat',
		required: ['firstName', 'lastName', 'emailAddress', 'birthDate', ...extendedRequired, 'privacy'],
		properties: getStudentProperties(extended),
	}
}

export default {
	onSubmit: async ({ formData }) => {
		submitFunction(formData, 'lead');
	},
	schema: getStudentDefinition(),
	uiSchema: getStudentUiSchema()
}

