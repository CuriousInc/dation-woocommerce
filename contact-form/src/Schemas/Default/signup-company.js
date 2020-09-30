import axios from 'axios';
import student from '../../Definitions/student';
import company from '../../Definitions/company';

export const submitFunction = (formData, endpoint) => {
  const headers = { 'Content-Type': 'application/json' };
  const instance = axios.create({ headers });
  const baseUrl = window.location.origin;

  const template = document.createElement('div');
  // Scroll to top of iframe and parent to make sure the notification is in sight
  window.parent.scrollTo(0, 0);
  window.scrollTo(0, 0);

  instance.request({
    method: 'post',
    data: JSON.stringify(formData),
    url: `${baseUrl}/wp-json/dationwoocommerce/v1/submit/${endpoint}`,
  }).then(() => {
    template.innerHTML = 'Bedankt voor uw reservering. Er wordt zo spoedig mogelijk contact met u opgenomen over uw inschrijving.';
    template.className = 'alert alert-success';

    const placeHolder = document.getElementById('alertPlaceHolder');
    placeHolder.append(template);
  }).catch(() => {
    template.innerHTML = 'Er is iets misgegaan bij het inschrijven. Probeer het opnieuw';
    template.className = 'alert alert-danger';
    const placeHolder = document.getElementById('alertPlaceHolder');
    placeHolder.append(template);
  });
};

export default {
  onSubmit: async ({ formData }) => {
    submitFunction(formData, 'companyLead');
  },
  schema: {
    definitions: {
      student: {
        ...student.definition,
      },
      company: {
        ...company.definition({}),
      },
    },

    required: [],
    properties: {
      company: {
        type: 'object',
        title: 'Bedrijf',
        $ref: '#/definitions/company',
      },
      students: {
        title: 'Leerlingen',
        type: 'array',
        minItems: 1,
        items: {
          $ref: '#/definitions/student',
        },
      },
    },
  },
  uiSchema: {
    students: {
      items: {
        ...student.uiSchema,
      },
      'ui:options': {
        orderable: false,
      },
    },
    company: {
      companyName: {
        classNames: 'form-input-sm',
      },
      address: {
        classNames: 'form-input-sm',
      },
      VATRegistration: {
        classNames: 'form-input-sm',
      },
      mobileNumber: {
        classNames: 'form-input-sm',
      },
      phoneNumber: {
        classNames: 'form-input-sm',
      },
      email: {
        classNames: 'form-input-sm',
      },
    },
  },
};
