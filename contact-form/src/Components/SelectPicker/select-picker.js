import React, {useState} from 'react';
import PropTypes from 'prop-types';

const SelectPicker = ({label, initialValue, required = false, options, inputClasses, labelClasses, wrapperClasses}) => {
	const [value, setValue] = useState(initialValue);

	return (
		<div className={wrapperClasses}>
			<label className={labelClasses}>{label}</label>
			<select value={value} onChange={e => setValue(e.target.value)} required={required} className={inputClasses}>
				{options.map(option => <option value={option.value} key={option.value}>{option.displayName}</option>)}
			</select>
		</div>
	)
};

SelectPicker.propTypes = {
	label: PropTypes.string.isRequired,
	initialValue: PropTypes.number,
	required: PropTypes.bool,
	options: PropTypes.shape({
		value: PropTypes.number,
		displayName: PropTypes.string,
	}),
	classes: PropTypes.string,
};

export default SelectPicker;