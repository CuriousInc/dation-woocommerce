import React from 'react';
import DateInput from '../Widgets/DateInput';

export const getBasicStudentSchema = () => ({
  firstName: {
    type: 'string',
    title: 'Voornaam',
    default: '',
  },
  lastName: {
    type: 'string',
    title: 'Achternaam',
  },
  gender: {
    type: 'string',
    title: 'Geslacht',
    enum: ['M', 'F'],
    enumNames: ['Man', 'Vrouw'],
  },
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
  mobileNumber: {
    type: 'string',
    title: 'Mobiele nummer',
    minLength: 10,
  },
  phoneNumber: {
    type: 'string',
    title: 'Telefoon nummer',
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
    title: 'Geboortedatum',
  },
});

export const getStudentProperties = () => ({
  ...getBasicStudentSchema(),
  nationalRegistryNumber: {
    type: 'string',
    title: 'Rijksregisternummer',
  },
  dateCLicence: {
    type: 'string',
    title: 'Datum rijbewijs C behaald',
  },
  dateDLicence: {
    type: 'string',
    title: 'Datum rijbewijs D behaald',
  },
  dateCode95: {
    type: 'string',
    title: 'Datum code 95',
  },
  dateMedicalExam: {
    type: 'string',
    title: 'Datum medische schifting',
  },
  receiveSms: {
    type: 'boolean',
    title: 'Ik wens een sms-reminder te ontvangen ter herinnering van de eerstvolgende nascholing.',
  },
  financedThroughSubsidy: {
    type: 'boolean',
    title: 'Financiering via subsidie',
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
});

export const getStudentUISchema = () => ({
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
  gender: {
    classNames: 'col-xs-12',
    'ui:widget': 'radio',
    'ui:options': {
      inline: true,
    },
  },
  birthDate: {
    classNames: 'form-input-sm col-xs-12 col-sm-6',
    'ui:widget': (props) => <DateInput {...props} />,
    'ui:options': {
      timeFormat: false,
    },
  },
  dateCLicence: {
    classNames: 'form-input-sm col-xs-12 col-sm-6',
    'ui:widget': (props) => <DateInput {...props} />,
  },
  dateDLicence: {
    classNames: 'form-input-sm col-xs-12 col-sm-6',
    'ui:widget': (props) => <DateInput {...props} />,
  },
  dateCode95: {
    classNames: 'form-input-sm col-xs-12 col-sm-6',
    'ui:widget': (props) => <DateInput {...props} />,
  },
  dateMedicalExam: {
    classNames: 'form-input-sm col-xs-12 col-sm-6',
    'ui:widget': (props) => <DateInput {...props} />,
  },
  nationalRegistryNumber: {
    classNames: 'form-input-sm col-xs-12',
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
  receiveSms: {
    classNames: 'col-xs-12',
  },
  financedThroughSubsidy: {
    classNames: 'col-xs-12',
  },
  privacy: {
    classNames: 'col-xs-12',
  },
});

const definition = {
  type: 'object',
  required: [
    'firstName',
    'lastName',
    'emailAddress',
    // 'privacy',
  ],
  properties: getBasicStudentSchema(),
};

const uiSchema = getStudentUISchema();

const belgianDefinition = {
  type: 'object',
  required: [
    'firstName',
    'lastName',
    'zipCode',
    'houseNumber',
    'street',
    'city',
    'mobileNumber',
    'birthPlace',
    'birthDate',
    'privacy',
  ],
  properties: {
    ...getStudentProperties(),
    emailAddress: {},
  },
};

export default {
  belgianDefinition,
  definition,
  uiSchema,
};
