import React from 'react';
import DateInput from './Widgets/DateInput';

export default {
  schema: {
    title: 'Inschrijving voor Training 26-12',
    description: 'Training.',
    type: 'object',
    required: [
      'firstName',
      'lastName',
    ],
    properties: {
      firstName: {
        type: 'string',
        title: 'Voornaam',
        default: '',
      },
      lastName: {
        type: 'string',
        title: 'Achternaam',
      },
      address: {
        type: 'integer',
        title: 'adres',
      },
      mobileNumber: {
        type: 'string',
        title: 'Mobiel nummer',
        minLength: 10,
      },
      email: {
        type: 'string',
        format: 'email',
        title: 'E-mail adres',
      },
      birthPlace: {
        type: 'string',
        title: 'Geboorteplaats',
      },
      birthDate: {
        type: 'string',
        format: 'date',
        title: 'Geboortedatum',
      },
      nationalRegistryNumber: {
        type: 'string',
        format: 'number',
        title: 'Rijksregisternummber',
      },
      dateCLicence: {
        type: 'string',
        format: 'date',
        title: 'Datum rijbewijs C behaald',
      },
      dateCode95: {
        type: 'string',
        format: 'date',
        title: 'Datum code 95',
      },
      dateMedicalExam: {
        type: 'string',
        format: 'date',
        title: 'Datum medische schifting',
      },
    },
    signupAsCompany: {
      type: 'boolean',
      title: 'Inschrijven als bedrijf?',
      $ref: '#/definitions/isCompany',
    },
  },
  uiSchema: {
    birthDate: {
      'ui:field': (props) => <DateInput {...props} />,
      'ui:options': {
        label: 'Geboorte datum',
      },
    },
    dateCLicence: {
      'ui:field': (props) => <DateInput {...props} />,
      'ui:options': {
        label: '',
      },
    },
    dateCode95: {
      'ui:field': (props) => <DateInput {...props} />,
      'ui:options': {
      },
    },
    dateMedicalExam: {
      'ui:field': (props) => <DateInput {...props} />,
      'ui:options': {
        label: '',
      },
    },
  },
};
