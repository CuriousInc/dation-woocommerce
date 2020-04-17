import {getBasicStudentSchema, getStudentUISchema} from '../Definitions/student';
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
			'privacy',
		],
		properties: getBasicStudentSchema(),
	},
	uiSchema: getStudentUISchema(),
};
