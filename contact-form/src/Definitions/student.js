import React from 'react';
import DateInput from '../Widgets/DateInput';

const definition = {
  type: 'object',
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
      type: 'string',
      title: 'Adres',
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
      title: 'Geboortedatum',
    },
    nationalRegistryNumber: {
      type: 'string',
      format: 'number',
      title: 'Rijksregisternummber',
    },
    dateCLicence: {
      type: 'string',
      title: 'Datum rijbewijs C behaald',
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
  },
};

const uiSchema = {
  birthDate: {
    'ui:widget': (props) => <DateInput {...props} />,
  },
  dateCLicence: {
    'ui:widget': (props) => <DateInput {...props} />,
  },
  dateCode95: {
    'ui:widget': (props) => <DateInput {...props} />,
  },
  dateMedicalExam: {
    'ui:widget': (props) => <DateInput {...props} />,
  },
};

export default {
  definition,
  uiSchema,
};
