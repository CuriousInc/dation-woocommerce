import React, { useRef } from 'react';
import { withTheme } from 'react-jsonschema-form';
import injectBootstrapCss from './Loaders/StyleLoader';
import Bootstrap4Theme from './bootstrap4-theme';

import SignupAsPrivate from './Schemas/signup-private';
import SignupAsCompany from './Schemas/signup-company';
import './assets/index.scss';

const isCompany = document.location.search.indexOf('company') !== -1;

const FormSchema = {
  ...(isCompany ? { ...SignupAsCompany } : { ...SignupAsPrivate }),
};

function App() {
  const formRef = useRef(null);
  injectBootstrapCss();
  const ThemedForm = withTheme(Bootstrap4Theme);

  return (
    <div className="App">
      <div className="container">
        <div className="col">
          <ThemedForm
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
