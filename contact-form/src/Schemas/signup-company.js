import axios from 'axios';
import student from '../Definitions/student';
import company from '../Definitions/company';

export default {
  onSubmit: async ({ formData }) => {
    const headers = { 'Content-Type': 'application/json' };
    const instance = axios.create({ headers });

    await instance.request({
      method: 'post',
      data: JSON.stringify(formData),
      url: 'https://cloud-dev.dation.nl:269/wp-json/dationwoocommerce/v1/submit/companyLead',
    });
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

    title: 'Inschrijving als bedrijf',
    description: '',
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
