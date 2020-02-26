import React from 'react';
import DateInput from '../Widgets/DateInput';


const companyDefinition = {
  companyName: {
    type: 'string',
    title: 'Bedrijfsnaam',
  },
  address: {
    type: 'string',
    title: 'Adres',
  },
  VATRegistration: {
    type: 'string',
    title: 'BTW nummer',
  },
  mobileNumber: {
    type: 'string',
    title: 'Mobiel nummer',
  },
  email: {
    type: 'string',
    format: 'email',
    title: 'E-mail adres',
  },

};

const studentDefinition = {
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
};

export default {
  onSubmit: ({ formData }) => { console.log('Data: ', formData); },
  onChange: (...args) => { console.log('Change: ', ...args); },
  onError: (...args) => { console.log('Error: ', ...args); },
  schema: {
    title: 'Inschrijving als bedrijf',
    description: '',
    type: 'object',
    required: [

    ],
    properties: {
      ...companyDefinition,
      students: {
        title: 'Leerlingen',
        type: 'array',
        items: {
          type: 'object',
          properties: {
            nested: {
              ...studentDefinition,
            },
          },
        },
      },
    },
  },
  uiSchema: {
    birthDate: {
      'ui:widget': (props) => <DateInput {...props} />,
      'ui:options': {
        wrapperClassNames: '',
      },
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
  },
};
