import { getBasicStudentSchema, getStudentUISchema } from '../../Definitions/student';
import { submitFunction } from './signup-company';

export default {
  onSubmit: async ({ formData }) => {
    submitFunction(formData, 'lead');
  },
  schema: {
    type: 'object',
    required: [
      'firstName',
      'lastName',
      'zipCode',
      'houseNumber',
      'street',
      'city',
      'mobileNumber',
      'birthPlace',
      'birthDate',
      'emailAddress',
    ],
    properties: getBasicStudentSchema(),
  },
  uiSchema: getStudentUISchema(),
};
