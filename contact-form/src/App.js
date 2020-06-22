import React, { useRef, useState } from 'react';
import Form from 'react-jsonschema-form';
import injectBootstrapCss from './Loaders/StyleLoader';

import SignupAsPrivate from './Schemas/signup-private';
import SignupAsCompany from './Schemas/signup-company';

function App({
  title, date, location, trainingId,
}) {
  injectBootstrapCss();

  const initialSchema = {
    ...{ ...SignupAsPrivate },
  };
  const [schema, setSchema] = useState(initialSchema);
  const [isCompany, setIsCompany] = useState(false);
  const [formFor, setFormFor] = useState('individual');

  // TODO: Localisation
  const transformErrors = (errors) => errors.map((error) => {
    let newError = {
      ...error,
    };
    if (error.name === 'required') {
      newError = {
        ...newError,
        message: 'Dit is een verplicht veld',
      };
    }
    return newError;
  });

  const formRef = useRef(null);

  const toggleSchema = () => {
    const newIsCompany = !isCompany;
    const newSchema = {
      ...(newIsCompany ? { ...SignupAsCompany } : { ...SignupAsPrivate }),
    };

    setIsCompany(newIsCompany);
    setSchema(newSchema);
  };
  return (
    <div className="App">
      <div className="container">
        <div id="alertPlaceHolder" />
        <div className="row">
          <div className="col-xs-6">
            <button
              type="button"
              onClick={() => {
                setFormFor('individual');
                toggleSchema();
              }}
              className={`${formFor === 'individual' ? 'btn btn-primary' : 'btn btn-default'} btn-block`}
            >Particulier
            </button>
            <button
              type="button"
              onClick={() => {
                setFormFor('company');
                toggleSchema();
              }}
              className={`${formFor === 'company' ? 'btn btn-primary' : 'btn btn-default'} btn-block`}
            >Bedrijven
            </button>
          </div>
          <div className="col-xs-6">
            <li className="list-group-item active ">
              <div className="form-group">
                <label>Inschrijven voor training:</label>
                <p className="form-control-static">{title}</p>
              </div>
              <div className="form-group">
                <label>Datum:</label>
                <p className="form-control-static">{date}</p>
              </div>
              <div className="form-group">
                <label>Locatie:</label>
                <p className="form-control-static">{location}</p>
              </div>
            </li>
          </div>
        </div>
        <div className="row">
          <div className="col">
            <Form
              ref={formRef}
              schema={schema.schema}
              uiSchema={schema.uiSchema}
              formData={{ trainingId }}
              onSubmit={schema.onSubmit}
              onChange={schema.onChange}
              onError={schema.onError}
              transformErrors={transformErrors}
              showErrorList={false}
              noHtml5Validate
            >
              <button type="submit" className="btn btn-primary pull-right">Verzenden</button>
            </Form>
          </div>
        </div>
      </div>
    </div>
  );
}

export default App;
