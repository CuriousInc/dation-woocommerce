import React, { useRef, useState } from 'react';
import Form from 'react-jsonschema-form';
import moment from 'moment';
import injectBootstrapCss from './Loaders/StyleLoader';

import SignupAsPrivate from './Schemas/Hoekstra/signup-private';
import SignupAsCompany from './Schemas/Hoekstra/signup-company';

const HoekstraApp = ({
  title, date, location, trainingId,
}) => {
  injectBootstrapCss();
  const initialSchema = {
    ...{ ...SignupAsPrivate },
  };

  const [schema, setSchema] = useState(initialSchema);
  const [formFor, setFormFor] = useState('individual');

  // TODO: Localisation
  const transformErrors = (errors) => errors.map((error) => {
    let newError = {
	  ...error,
    };

    if(error.name === 'format' && error.params.format === 'email') {
      newError = {
        ...newError,
        message: 'Voer een geldig e-mailadres in',
      };
    }
    if (error.name === 'required') {
	  newError = {
        ...newError,
        message: 'Dit is een verplicht veld',
	  };
    }
    return newError;
  });

  const formRef = useRef(null);

  const toggleSchema = (type) => {
    if (type === 'company') {
	  setSchema(SignupAsCompany);
    } else {
	  setSchema(SignupAsPrivate);
    }
  };

  const isInvalidDate = (birthDateString) => {
    const birthDateMoment = moment(birthDateString, 'DD-MM-YYYY', true);
    if(!birthDateMoment.isValid() || birthDateMoment.format('DD-MM-YYYY') === "Invalid date") {
      return true;
    }

    return false
  }

  const validate = (formData, errors) => {
    const { birthDate, students } = formData;
    if (birthDate) {
      if(isInvalidDate(birthDate)) {
        errors.birthDate.addError('Formaat niet herkend. Gebruik dd-mm-yyyy');
      }
    }

    if (students) {
	  students.forEach((student, key) => {
	    if(student.birthDate) {
	      if(isInvalidDate(student.birthDate)) {
            errors.students[key].birthDate = { __errors: ['Formaat niet herkend. Gebruik dd-mm-yyyy'] };
          }
        }
	  });
    }

    return errors;
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
			    toggleSchema('company');
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
              formData={{ trainingId, titel: title, datum: date, firstName: undefined }}
              onSubmit={schema.onSubmit}
              onChange={schema.onChange}
              onError={schema.onError}
              transformErrors={transformErrors}
              showErrorList={false}
              noHtml5Validate
              validate={validate}
            >
              <button type="submit" className="btn btn-primary pull-right">Verzenden</button>
            </Form>
          </div>
        </div>
      </div>
    </div>
  );
};

export default HoekstraApp;
