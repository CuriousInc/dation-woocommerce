import React, { useRef } from 'react';
import Form from 'react-jsonschema-form';
import injectBootstrapCss from './Loaders/StyleLoader';

import SignupAsPrivate from './Schemas/signup-private';
import SignupAsCompany from './Schemas/signup-company';

import './assets/index.scss';


const isCompany = document.location.search.indexOf('company') !== -1;

const FormSchema = {
  ...(isCompany ? { ...SignupAsCompany } : { ...SignupAsPrivate }),
};

// TODO: Localisation
const transformErrors = (errors) => errors.map((error) => {
  const newError = {
    ...error,
  };
  return newError;
});


function App() {
  const formRef = useRef(null);
  injectBootstrapCss();

  return (
    <div className="App">
      <div className="container">
        <div className="col">
          <Form
            ref={formRef}
            schema={FormSchema.schema}
            uiSchema={FormSchema.uiSchema}
            onSubmit={FormSchema.onSubmit}
            onChange={FormSchema.onChange}
            onError={FormSchema.onError}
            transformErrors={transformErrors}
            showErrorList={false}
            localize={localize}
            noHtml5Validate
          />
        </div>
      </div>
    </div>
  );
}

export default App;
