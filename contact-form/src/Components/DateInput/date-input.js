import React, { useState } from 'react';
import PropTypes from 'prop-types';
import DateTime from 'react-datetime';
import moment from 'moment';
import icons from 'glyphicons';

import 'moment/locale/nl';
import './date-input.css';

// This component is only usable in combination with Bootstrap Style loader.
const DateInput = ({
  label = 'datepicker', initialValue, name, wrapperClasses, labelClasses, inputClasses,
}) => {
  const [value, setValue] = useState(initialValue);

  const handleChange = (inputValue) => {
    if (moment.isMoment(inputValue)) {
      setValue(inputValue.format('DD-MM-YYYY'));
    }
  };

  return (
    <div className={wrapperClasses}>
      <label className={labelClasses}>{label}</label>
      <div className="input-group">
        <DateTime
          dateFormat="DD-MM-YYYY"
          timeFormat={false}
          closeOnSelect
          locale="nl"
          value={value}
          inputProps={{
            name,
          }}
          className={inputClasses}
          onChange={handleChange}
        />
        <div className="input-group-append">
          <span className="input-group-text">
            {icons.calendar}
          </span>
        </div>
      </div>
    </div>
  );
};

DateInput.propTypes = {
  label: PropTypes.string.isRequired,
  name: PropTypes.string.isRequired,
  initialValue: PropTypes.string,
  wrapperClasses: PropTypes.string,
  labelClasses: PropTypes.string,
  inputClasses: PropTypes.string,
};

DateInput.defaultProps = {
  initialValue: '',
  wrapperClasses: '',
  labelClasses: '',
  inputClasses: '',
};
export default DateInput;
