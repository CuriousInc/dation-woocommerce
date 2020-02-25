
import React from 'react';

export default {
  schema: {
    title: 'Widgets',
    type: 'object',
    properties: {
      selectWidgetOptions: {
        title: 'Custom select widget with options',
        type: 'string',
        enum: ['foo', 'bar'],
        enumNames: ['Foo', 'Bar'],
      },
    },
  },
  uiSchema: {
    selectWidgetOptions: {
      'ui:widget': ({ value, onChange, options }) => {
        const { enumOptions, backgroundColor } = options;
        return (
          <select
            className="form-control"
            style={{ backgroundColor }}
            value={value}
            onChange={(event) => onChange(event.target.value)}
          >
            {enumOptions.map(({ label, value }, i) => (
              <option key={i} value={value}>
                {label}
              </option>
            ))}
          </select>
        );
      },
    },
  },
};
