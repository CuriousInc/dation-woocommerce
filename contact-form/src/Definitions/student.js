import React from 'react';
import DateInput from '../Widgets/DateInput';

export const getStudentProperties = () => ({
  ...getBasicStudentSchema(),
  nationalRegistryNumber: {
    type: 'string',
    title: 'Rijksregisternummber',
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
  privacy: {
    type: 'boolean',
    title: 'Akkoord met de privacyverklaring en algemene voorwaarden',
  },
  trainingId: {
    type: 'string',
  },
});

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
})

export const getStudentUISchema = () => ({
  gender: {
    'ui:widget': 'radio',
    'ui:options': {
      inline: true,
    },
  },
  birthDate: {
    'ui:widget': (props) => <DateInput {...props} />,
    'ui:options': {
      timeFormat: false,
    },
  },
  dateCLicence: {
    'ui:widget': (props) => <DateInput {...props} />,
  },
  dateDLicence: {
    'ui:widget': (props) => <DateInput {...props} />,
  },
  dateCode95: {
    'ui:widget': (props) => <DateInput {...props} />,
  },
  dateMedicalExam: {
    'ui:widget': (props) => <DateInput {...props} />,
  },
  trainingId: {
    'ui:widget': 'hidden',
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

export default {
  definition,
  uiSchema,
};
