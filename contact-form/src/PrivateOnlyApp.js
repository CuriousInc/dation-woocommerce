import React, { useRef } from 'react';
import Form from 'react-jsonschema-form';
import injectBootstrapCss from './Loaders/StyleLoader';

function PrivateOnlyApp({
  education, packageName, signupAsPrivate,
}) {
  injectBootstrapCss();

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

  return (
    <div className="App">
      <div className="container">
        <div id="alertPlaceHolder" />
        <div className="row">
          <div
            className="col-xs-12 col-sm-6"
            style={{
					  paddingBottom: '1em',
					  paddingLeft: 0,
            }}
          >
            <li className="list-group-item active ">
              <div className="form-group">
                <label>Opleiding:</label>
                <p className="form-control-static">{education}</p>
              </div>
              <div className="form-group">
                <label>Pakket:</label>
                <p className="form-control-static">{packageName}</p>
              </div>
            </li>
          </div>
        </div>
        <div className="row">
          <div className="col">
            <Form
              ref={formRef}
              schema={signupAsPrivate.schema}
              uiSchema={signupAsPrivate.uiSchema}
              formData={{ education, packageName }}
              onSubmit={signupAsPrivate.onSubmit}
              onChange={signupAsPrivate.onChange}
              onError={signupAsPrivate.onError}
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

export default PrivateOnlyApp;
