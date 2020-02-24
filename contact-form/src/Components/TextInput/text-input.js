import React, {useState} from 'react';
import PropTypes from 'prop-types';

const TextInput = ({label, initialValue, required = false, name, wrapperClasses, labelClasses, inputClasses}) => {
	const [value, setValue] = useState(initialValue);

	return (
		<div className={wrapperClasses}>
			<label className={labelClasses}>{label}</label>
			<input type='text' name={name} className={inputClasses} value={value} onChange={e => setValue(e.target.value)}
				   required={required}/>
		</div>
	);
};

TextInput.propTypes = {
	name: PropTypes.string.isRequired,
	label: PropTypes.string.isRequired,
	initialValue: PropTypes.string,
	required: PropTypes.bool,
	wrapperClasses: PropTypes.string,
	labelClasses: PropTypes.string,
	inputClasses: PropTypes.string,
};

export default TextInput;