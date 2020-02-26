import React, { useState } from 'react';
import PropTypes from 'prop-types';
import DateTime from 'react-datetime';
import moment from 'moment';
import icons from 'glyphicons';
import cn from 'classnames';
import 'moment/locale/nl';
import './date-input.css';

// This component is only usable in combination with Bootstrap Style loader.
const DateInput = ({
  id,
  value,
  name,
  required,
  disabled,
  onChange,
  options,
}) => {
  const [fieldValue, setFieldValue] = useState(value);

  const handleChange = (inputValue) => {
    if (moment.isMoment(inputValue)) {
      const val = inputValue.format('DD-MM-YYYY');

      setFieldValue(val);
      typeof onChange === 'function' && onChange(val);
    }
  };

  return (
    <div className={cn('input-group', options.wrapperClassNames)}>
      {options.label && <label>{options.label}</label>}
      <DateTime
        dateFormat={options.dateformat}
        timeFormat={options.timeformat}
        closeOnSelect
        locale="nl"
        value={fieldValue}
        inputProps={{ name }}
        onChange={handleChange}
        className={options.cssClassNames}
        required={required}
        disabled={disabled}
        id={id}
      />
      <div className="input-group-addon">
        <span className="input-group-text">
          {icons.calendar}
        </span>
      </div>
    </div>
  );
};

DateInput.schema = {
  type: 'object',
  properties: {
    date: {
      type: 'string',
    },
  },
};

DateInput.propTypes = {
  id: PropTypes.string,
  value: PropTypes.string,
  name: PropTypes.string,
  required: PropTypes.bool,
  disabled: PropTypes.bool,
  onChange: PropTypes.func,
  options: PropTypes.shape({
    dateformat: PropTypes.string,
    timeformat: PropTypes.string,
    wrapperClassNames: PropTypes.string,
    cssClassNames: PropTypes.string,
    label: PropTypes.string,
  }),
};

DateInput.defaultProps = {
  id: '',
  value: '',
  name: '',
  required: false,
  disabled: false,
  onChange: null,
  options: {
    dateformat: 'DD-MM-YY',
    timeformat: null,
    wrapperClassNames: '',
    cssClassNames: '',
    label: '',
  },
};
export default DateInput;
