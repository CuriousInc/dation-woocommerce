import student from '../../Definitions/student';
import company from '../../Definitions/company';
import { submitFunction } from '../Default/signup-company';

export default {
  onSubmit: async ({ formData }) => {
    submitFunction(formData, 'companyLead');
  },
  schema: {
    definitions: {
      student: {
        ...student.belgianDefinition,
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
        title: 'Kandidaat',
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
