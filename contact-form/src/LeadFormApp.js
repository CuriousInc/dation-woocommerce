import React, { useRef, useState } from 'react';
import Form from 'react-jsonschema-form';
import injectBootstrapCss from "./Loaders/StyleLoader";

import './assets/index.scss';

import leadSchema from './Schemas/lead-form'


function LeadFormApp({
				 title, date, location, trainingId,
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
				<div className="row" style={{marginBottom: 16}}>
					<div className="col">
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
							schema={leadSchema.schema}
							uiSchema={leadSchema.uiSchema}
							formData={{ trainingId }}
							onSubmit={leadSchema.onSubmit}
							onChange={leadSchema.onChange}
							onError={leadSchema.onError}
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
};

export default LeadFormApp;