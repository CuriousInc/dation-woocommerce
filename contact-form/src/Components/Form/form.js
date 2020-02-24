import React, {Component} from 'react';
import ComponentLoader from '../../Loaders/ComponentLoader';

class Form extends Component {
	constructor(props) {
		super(props);
		this.formRef = React.createRef();
	}

	render() {
		const {config} = this.props;
		const {inputs, classes, submitButton} = config;
		const {url, inputClasses, text} = submitButton;


		const onSubmit = () => {
			const submittedForm = this.formRef.current;
			console.log('submitting');
			console.log(submittedForm);
			//TODO: serialize form and submit to url. Also errorHandling.
			console.error('this should submit using url prop');
		};
		return (
			<form className={classes} ref={this.formRef}>
				{inputs.map(input => <ComponentLoader key={input.name} componentName={input.type} componentProps={{...input}}/>)}
				<button type="submit" className={inputClasses} onSubmit={e => {
					console.log('submitting1');
					e.preventDefault();
					onSubmit();
				}}>{text}</button>
			</form>
		)
	}
};

export default Form;