import React, { useRef } from 'react';
import axios from 'axios';
import Form from 'react-jsonschema-form';
import schema from './form-schema';
import './App.css';
import { injectBootstrapCss } from './Loaders/StyleLoader';

function App() {
  const formRef = useRef(null);

  injectBootstrapCss();

  const handleSubmit = async ({ formData }) => {
    const { submit } = schema;
    const headers = {
      'Content-Type': 'application/json',
    };

    const instance = axios.create({headers});

    const response = await instance.request({
      method: 'post',
      data: JSON.stringify(formData),
      url: submit.url,
    });

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
