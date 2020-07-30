import { getStudentProperties, getStudentUISchema } from '../../Definitions/student';
import { submitFunction } from '../Default/signup-company';

export default {
  onSubmit: async ({ formData }) => {
    submitFunction(formData, 'lead');
  },
  schema: {
    type: 'object',
    title: 'Particulier',
    required: [
      'firstName',
      'lastName',
      'zipCode',
      'houseNumber',
      'street',
      'city',
      'mobileNumber',
      'emailAddress',
      'birthPlace',
      'birthDate',
      'privacy',
    ],
    properties: getStudentProperties(),
  },
  uiSchema: getStudentUISchema(),
};
