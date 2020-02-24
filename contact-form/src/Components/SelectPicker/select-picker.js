import React, {useState} from 'react';
import PropTypes from 'prop-types';

const SelectPicker = ({label, initialValue, required = false, options, inputClasses, labelClasses, wrapperClasses}) => {
	const [value, setValue] = useState(initialValue);

	return (
		<div className={wrapperClasses}>
			<label className={labelClasses}>{label}</label>
			<select value={value} onChange={e => setValue(e.target.value)} required={required} className={inputClasses}>
				{options.map(option => <option value={option.value} key={option.value}>{option.label}</option>)}
			</select>
		</div>
	)
};

SelectPicker.propTypes = {
	label: PropTypes.string.isRequired,
	initialValue: PropTypes.string,
	required: PropTypes.bool,
	options: PropTypes.arrayOf(
	PropTypes.shape({
		value: PropTypes.string,
		label: PropTypes.string,
	})),
	classes: PropTypes.string,
	inputClasses: PropTypes.string,
	labelClasses: PropTypes.string,
	wrapperClasses: PropTypes.string,
};

export default SelectPicker;
