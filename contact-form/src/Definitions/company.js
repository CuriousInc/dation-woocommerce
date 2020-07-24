const definition = {
  type: 'object',
  required: [
    'companyName',
    'email',
  ],
  properties: {
    companyName: {
      type: 'string',
      title: 'Bedrijfsnaam',
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
      title: 'Telefoon nummer',
      minLength: 10,
    },
    email: {
      type: 'string',
      format: 'email',
      title: 'E-mailadres',
    },
  },
};

export default {
  definition,
};
