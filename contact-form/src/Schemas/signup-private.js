import React from 'react';
import axios from 'axios';
import DateInput from '../Widgets/DateInput';

export default {
  onSubmit: async ({ formData }) => {
    const headers = { 'Content-Type': 'application/json' };
    const instance = axios.create({ headers });

    await instance.request({
      method: 'post',
      data: JSON.stringify(formData),
      url: 'https://cloud-dev.dation.nl:269/wp-json/dationwoocommerce/v1/submit/lead',
    });
  },
  schema: {
    type: 'object',
    required: [
      'firstName',
      'lastName',
      'privacy',
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
        type: 'string',
        title: 'Adres',
      },
      mobileNumber: {
        type: 'string',
        title: 'Mobiel nummer',
        minLength: 10,
      },
      emailAddress: {
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
      trainingId: {
        type: 'string',
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
    trainingId: {
      'ui:widget': 'hidden',
    },
  },
};
