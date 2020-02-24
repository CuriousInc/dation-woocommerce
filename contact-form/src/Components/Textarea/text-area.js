import React, {useState} from 'react';
import PropTypes from 'prop-types';

const TextArea =({label, initialValue, required = false, wrapperClasses, inputClasses, labelClasses}) => {
	const [value, setValue] = useState(initialValue);

	return (
	<div className={wrapperClasses}>
		<label className={labelClasses}>{label}</label>
		<textarea required={required} className={inputClasses} value={value} onChange={e => setValue(e.target.value)} />
	</div>
	);
};

TextArea.propTypes = {
	label: PropTypes.string.isRequired,
	initialValue: PropTypes.string,
	classes: PropTypes.string,
	required: PropTypes.bool,
};

export default TextArea;