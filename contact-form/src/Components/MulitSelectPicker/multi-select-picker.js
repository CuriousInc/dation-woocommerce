import React, {useState} from 'react';
import Select  from 'react-select';
import PropTypes  from 'prop-types';

const MultiSelectPicker = ({label, initialOptions, name, options, wrapperClasses, labelClasses, inputClasses}) => {
	const [selectedOptions, setSelectedOptions] = useState(initialOptions);

	const handleChange = (newValue) => {
		console.log(newValue);
	}

	return (
		<div className={wrapperClasses}>
			<label className={labelClasses}>{label}</label>
			<Select
				isMulti={true}
				name={name}
				options={options}
				value={selectedOptions}
				onChange={options => setSelectedOptions(options)}
				className={inputClasses}
			/>
		</div>
	)
};

MultiSelectPicker.propTypes = {
	name: PropTypes.string,
	label: PropTypes.string,
	initialOptions: PropTypes.shape({
		value: PropTypes.string,
		label: PropTypes.string,
		selected: PropTypes.bool,
	})
}

export default MultiSelectPicker;