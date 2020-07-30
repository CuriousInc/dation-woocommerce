const definition = ({ extraProperties = {}, extraRequired = [] }) => ({
  type: 'object',
  required: [
    'companyName',
    'email',
    ...extraRequired,
  ],
  properties: {
    companyName: {
      type: 'string',
      title: 'Bedrijfsnaam',
    },
    contactPerson: {
      type: 'string',
      title: 'Contactpersoon',
    },
    address: {
      type: 'string',
      title: 'Adres',
    },
    VATRegistration: {
      type: 'string',
      title: 'BTW-nummer',
    },
    mobileNumber: {
      type: 'string',
      title: 'Mobiele nummer',
      minLength: 10,
    },
    phoneNumber: {
      type: 'string',
      title: 'Telefoonnummer',
      minLength: 10,
    },
    email: {
      type: 'string',
      format: 'email',
      title: 'E-mailadres',
    },
    emailInvoice: {
      type: 'string',
      format: 'email',
      title: 'E-mailadres factuur',
    },
    ...extraProperties,
  },
});

export default {
  definition,
};
