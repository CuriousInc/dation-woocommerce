export function injectBootstrapCss() {
  const bsStyleTag = document.createElement('link');
  bsStyleTag.setAttribute('rel', 'stylesheet');
  bsStyleTag.setAttribute('integrety', 'sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T');
  bsStyleTag.setAttribute('href', 'https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css');
  bsStyleTag.setAttribute('crossOrigin', 'anonymous');

  const bootswatchDarklyTag = document.createElement('link');
  bootswatchDarklyTag.setAttribute('rel', 'stylesheet');
  bootswatchDarklyTag.setAttribute('href', 'https://stackpath.bootstrapcdn.com/bootswatch/4.4.1/darkly/bootstrap.min.css');
  bootswatchDarklyTag.setAttribute('crossOrigin', 'anonymous');

  document.getElementsByTagName('head')[0].appendChild(bsStyleTag);
  // document.getElementsByTagName('head')[0].appendChild(bootswatchDarklyTag);
}


export const updateConfigClasses = (config) => {
  const { form: { inputs } } = config;

  const updatedInputs = inputs.map((input) => ({
    ...input,
    labelClasses: updateLabelClasses(input),
    inputClasses: updateInputClasses(input),
    wrapperClasses: updateWrapperClasses(input),
  }));

  return {
    ...config,
    card: {
      ...config.card,
      classes: `card ${config?.card?.classes ? config.card.classes : ''}`,
    },
    cardHeader: {
      ...config.cardHeader,
      classes: `card-header ${config?.cardHeader?.classes ? config.cardHeader.classes : ''}`,
    },
    cardTitle: {
      ...config.cardTitle,
      classes: `card-title ${config?.cardTitle?.classes ? config.cardTitle.classes : ''}`,
    },
    cardBody: {
      ...config.cardBody,
      classes: `card-body ${config?.cardBody?.classes ? config.cardBody.classes : ''}`,
    },
    form: {
      ...config.form,
      inputs: updatedInputs,
    },
  };
};

const updateInputClasses = (input) => {
  switch (input.type) {
    case 'DateInput':
      return input?.inputClasses ? input.inputClasses : '';
    case 'CheckBox':
      return `form-check-input ${input?.inputClasses ? input.inputClasses : ''}`;
    case 'MultiSelectPicker':
      return input?.inputClasses ? input.inputClasses : '';
    default:
      return `form-control ${input?.inputClasses ? input.inputClasses : ''}`;
  }
};

const updateWrapperClasses = (input) => {
  switch (input.type) {
    case 'CheckBox':
      return `form-check ${input?.wrapperClasses ? input.wrapperClasses : ''}`;
    default:
      return `form-group ${input?.wrapperClasses ? input.wrapperClasses : ''}`;
  }
};

const updateLabelClasses = (input) => {
  switch (input.type) {
    case 'CheckBox':
      return `form-check-label ${input?.labelClasses ? input.labelClasses : ''}`;
    default:
      return `col-form-label ${input?.labelClasses ? input.labelClasses : ''}`;
  }
};
