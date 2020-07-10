/* eslint-disable global-require */
import React from 'react';
import ReactDOM from 'react-dom';
import App from './App';
import LeadFormApp from './LeadFormApp';

import './assets/index.scss';

const element = document.getElementById('app');
const type = window.frameElement.getAttribute('data-type');

// Rendered in iframe so we take the location of the parent.
const urlParams = new URLSearchParams(window.parent.location.search);

const props = {
  title: urlParams.get('dw_trainingName') || 'Training onbekend',
  date: urlParams.get('dw_date') || 'Datum onbekend',
  location: urlParams.get('dw_location') || 'Locatie onbekend',
  trainingId: urlParams.get('dw_trainingId') || 'Training onbekend',
};

switch (type) {
  case 'kempische':
    require('./assets/belgianStyles.scss');
    ReactDOM.render(<LeadFormApp {...props} />, element);
    break;
  default:
    require('./assets/defaultStyles.scss');
    ReactDOM.render(<App {...props} />, element);
}
