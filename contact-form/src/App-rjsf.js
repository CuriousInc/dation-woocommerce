import React, { useRef } from 'react';
import axios from 'axios';
import Form from 'react-jsonschema-form';
import SignupAsPrivate from './Schemas/signup-private';
import SignupAsCompany from './Schemas/signup-company';
import './App.css';
import { injectBootstrapCss } from './Loaders/StyleLoader';

const isCompany = document.location.search.indexOf('company') !== -1;

const FormSchema = {
  ...(isCompany ? { ...SignupAsCompany } : { ...SignupAsPrivate }),
};

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
          />
        </div>
      </div>
    </div>
  );
}

export default App;
