import React from 'react';
import { submitFunction } from '../Default/signup-company';
import DateInput from '../../Widgets/DateInput';


function getStudentProperties() {
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
      title: 'E-mailadres',
    },
    birthPlace: {
      type: 'string',
      title: 'Geboorteplaats',
    },
    birthDate: {
      type: 'string',
      title: 'Geboortedatum (dd-mm-jjjj)',
    },
    nationalRegistryNumber: {
      type: 'string',
      title: 'Rijksregisternummer (Achterzijde EID)',
    },
    idCardNumber: {
      type: 'string',
      title: 'Identiteitskaartnummer (Voorzijde EID)',
    },
    location: {
      type: 'string',
      title: 'Locatie',
      enum: ['Lier', 'Lille', 'Geel', 'Kasterlee'],
    },
    automaticTransmission: {
      type: 'boolean',
      title: 'Ik wil rijlessen met een automaat',
    },
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
    availability: {
      type: 'string',
      title: 'Wanneer kan u zich vrijmaken om de lessen te volgen?',
    },
    privacy: {
      type: 'boolean',
      title: 'Akkoord met de privacyverklaring en algemene voorwaarden.',
    },
    education: {
      type: 'string',
    },
    packageName: {
      type: 'string',
    },
  };
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
    idCardNumber: {
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
    automaticTransmission: {
      classNames: 'col-xs-12',
    },
    location: {
      classNames: 'form-input-sm col-xs-12',
    },
    availability: {
      'ui:widget': 'textarea',
      classNames: 'form-input-sm col-xs-12',
    },
  };
}

export function getStudentDefinition() {
  return {
    type: 'object',
    title: 'Kandidaat',
    required: [
      'firstName',
      'lastName',
      'street',
      'houseNumber',
      'zipCode',
      'city',
      'mobileNumber',
      'emailAddress',
      'birthPlace',
      'birthDate',
      'idCardNumber',
      'nationalRegistryNumber',
      'privacy',
      'location',
    ],
    properties: getStudentProperties(),
  };
}

export default {
  onSubmit: async ({ formData }) => {
    submitFunction(formData, 'lead');
  },
  schema: getStudentDefinition(),
  uiSchema: getStudentUiSchema(),
};
