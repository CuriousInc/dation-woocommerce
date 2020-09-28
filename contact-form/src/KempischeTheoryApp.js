import React, { useRef, useState } from 'react';
import injectBootstrapCss from './Loaders/StyleLoader';

import SignupAsPrivate from './Schemas/Kempische/theory/signup-private';
import SignupAsCompany from './Schemas/Kempische/theory/signup-company';
import Form from "react-jsonschema-form";

const KempischeTheoryApp = ({ education, packageName, type }) => {
	injectBootstrapCss();

	const initialSchema = {
		...{ ...SignupAsPrivate(type) },
	};

	const [schema, setSchema] = useState(initialSchema);
	const [formFor, setFormFor] = useState('individual');

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

	const toggleSchema = (type) => {
		if (type === 'company') {
			setSchema(SignupAsCompany(type));
		} else {
			setSchema(SignupAsPrivate(type));
		}
	};
	return (
		<div className="App">
			<div className="container">
				<div id="alertPlaceHolder" />
				<div className="row mb-3">
					<div className="col-xs-12 col-sm-6" style={{ marginBottom: '2rem' }}>
						<button
							type="button"
							onClick={() => {
								setFormFor('individual');
								toggleSchema('individual');
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
					<div className="col-xs-12 col-sm-6">
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
							schema={schema.schema}
							uiSchema={schema.uiSchema}
							formData={{ packageName, education }}
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
	)

}

export default KempischeTheoryApp;