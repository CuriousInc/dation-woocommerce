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
      'privacy',
    ],
    properties: {
      ...getBasicStudentSchema(),
      newsLetter: {
        type: 'boolean',
        title: 'Ik blijf graag op de hoogte van het laatste nieuws en ontwikkelingen'
      },
      privacy: {
        type: 'boolean',
        title: 'Akkoord met de privacyverklaring en algemene voorwaarden.',
      },
    },
  },
  uiSchema: {
    ...getStudentUISchema(),
    newsLetter: {
      classNames: 'col-xs-12',
    }
  },
};
