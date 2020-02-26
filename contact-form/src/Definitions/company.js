export default {
  type: 'object',
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
      title: 'BTW nummer',
    },
    mobileNumber: {
      type: 'string',
      title: 'Mobiel nummer',
    },
    email: {
      type: 'string',
      format: 'email',
      title: 'E-mail adres',
    },
  },
};
