import axios from 'axios';
import student from '../Definitions/student';
import company from '../Definitions/company';

export const submitFunction = (formData, endpoint) => {
  const headers = { 'Content-Type': 'application/json' };
  const instance = axios.create({ headers });
  const baseUrl = window.location.origin;

  const template = document.createElement('div');
  window.parent.scrollTo(0, 0);
  window.scroll(0, 0);

  instance.request({
    method: 'post',
    data: JSON.stringify(formData),
    url: `${baseUrl}/wp-json/dationwoocommerce/v1/submit/${endpoint}`,
  }).then(() => {
    template.innerHTML = 'Inschrijving voldaan';
    template.className = 'alert alert-success';

    const placeHolder = document.getElementById('alertPlaceHolder');
    placeHolder.append(template);
    // Show success message and redirect to home after a short timeout
    setTimeout(() => {
      window.parent.location.replace(baseUrl);
    }, 3000);
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
        ...company.definition,
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
  },
};
