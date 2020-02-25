import React, { useRef } from 'react';
import Form from 'react-jsonschema-form';
import { injectBootstrapCss } from './Loaders/StyleLoader';
import schema from './form-schema';
import './App.css';


function App() {
  const formRef = useRef(null);

  injectBootstrapCss();

  const handleSubmit = ({ formData }) => {
    console.log('Data: ', formData);
  };

  return (
    <div className="App">
      <div className="container">
        <div className="col-6">
          <Form
            ref={formRef}
            schema={schema}
            onSubmit={handleSubmit}
            onChange={() => console.log('Change')}
            onError={() => console.log('Errors')}
          />
        </div>
      </div>
    </div>
  );
}

export default App;
