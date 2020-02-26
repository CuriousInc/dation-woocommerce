import student from '../Definitions/student';
import companyDefinition from '../Definitions/company';


export default {
  onSubmit: ({ formData }) => { console.log('Data: ', formData); },
  onChange: (...args) => { console.log('Change: ', ...args); },
  onError: (...args) => { console.log('Error: ', ...args); },
  schema: {
    definitions: {
      student: {
        ...student.definition,
      },
      company: {
        ...companyDefinition,
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
        minItems: '1',
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
