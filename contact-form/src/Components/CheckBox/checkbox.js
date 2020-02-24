import React, {useState} from 'react';

const Checkbox = props => {
	const {label, name, initialValue = false, labelClasses, inputClasses, wrapperClasses} = props;
	const [isChecked, setChecked] = useState(initialValue);

	return <div className={wrapperClasses}>
		<input className={inputClasses} type='checkbox' value='1' name={name} id={name} checked={isChecked}
			   onChange={() => setChecked(!isChecked)}/>
		<label className={labelClasses} htmlFor={name}>{label}</label>
	</div>
};

export default Checkbox;