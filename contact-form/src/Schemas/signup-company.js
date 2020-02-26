import React from 'react';
import DateInput from '../Widgets/DateInput';
import studentDefinition from '../Definitions/student';
import companyDefinition from '../Definitions/company';


export default {
  onSubmit: ({ formData }) => { console.log('Data: ', formData); },
  onChange: (...args) => { console.log('Change: ', ...args); },
  onError: (...args) => { console.log('Error: ', ...args); },
  schema: {
    definitions: {
      student: {
        ...studentDefinition,
      },
      company: {
        ...companyDefinition,
      },
    },

    title: 'Inschrijving als bedrijf',
    description: '',
    type: 'object',
    required: [

    ],
    properties: {
      company: {
        title: 'Bedrijf',
        $ref: '#/definitions/company',
      },
      students: {
        title: 'Leerlingen',
        type: 'array',
        items: {
          $ref: '#/definitions/student',
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
