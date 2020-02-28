import React, { useRef, useState } from 'react';
import Form from 'react-jsonschema-form';
import injectBootstrapCss from './Loaders/StyleLoader';

import SignupAsPrivate from './Schemas/signup-private';
import SignupAsCompany from './Schemas/signup-company';

import './assets/index.scss';

function App({
  title, date, location, trainingId,
}) {
  injectBootstrapCss();

  const initialSchema = {
    ...{ ...SignupAsPrivate },
  };
  const [schema, setSchema] = useState(initialSchema);
  const [isCompany, setIsCompany] = useState(false);

  // TODO: Localisation
  const transformErrors = (errors) => errors.map((error) => {
    const newError = {
      ...error,
    };
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
        <div className="row">
          <div className="card">
            <div className="card-body">
              <div className="col-xs-3">
                <button type="button" onClick={toggleSchema} className="btn btn-default">{isCompany ? 'Switch naar particulier' : 'Switch naar bedrijven'}</button>
              </div>
              <div className="col-xs-9">
                <fieldset>
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
                </fieldset>
              </div>
            </div>
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
              <button type="submit" className="btn btn-primary">Verzenden</button>
            </Form>
          </div>
        </div>
      </div>
    </div>
  );
}

export default App;
