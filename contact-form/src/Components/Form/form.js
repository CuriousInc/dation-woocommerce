import React from 'react';
import ComponentLoader from '../../Loaders/ComponentLoader';

const Form = ({config}) => {
	const { inputs, classes, submitButton } = config;
	const {url, inputClasses, text} = submitButton;

	const onSubmit = () => {
		//TODO: serialize form and submit to url. Also errorHandling.
		console.error('this should submit using url prop');
	};

	return (
		<form className={classes}>
			{inputs.map(input => <ComponentLoader key={input.name} componentName={input.type} componentProps={{...input}}/>)}
			<button type="submit" className={inputClasses} onSubmit={() => onSubmit()}>{text}</button>
		</form>
	)
};

export default Form;