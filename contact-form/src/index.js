/* eslint-disable global-require */
import React from 'react';
import ReactDOM from 'react-dom';
import moment from 'moment';
import DefaultApp from './DefaultApp';
import KempischeApp from './KempischeApp';

import './assets/index.scss';
import HoekstraApp from './HoekstraApp';

const element = document.getElementById('app');
const type = window.frameElement.getAttribute('data-type');

// Rendered in iframe so we take the location of the parent.
const urlParams = new URLSearchParams(window.parent.location.search);

const startDate = moment(urlParams.get('dw_start_date'));
const endDate = moment(urlParams.get('dw_end_date'));

const date = `${startDate.format('DD-MM-YYYY HH:mm')}-${endDate.format('HH:mm')}`;

const props = {
  title: urlParams.get('dw_trainingName') || 'Training onbekend',
  date,
  location: urlParams.get('dw_location') || 'Locatie onbekend',
  trainingId: urlParams.get('dw_trainingId') || 'Training onbekend',
};

switch (type) {
  case 'kempische':
    require('./assets/belgianStyles.scss');
    ReactDOM.render(<KempischeApp {...props} />, element);
    break;
  case 'hoekstra':
    require('./assets/defaultStyles.scss');
    ReactDOM.render(<HoekstraApp {...props} />, element);
    break;
  default:
    require('./assets/defaultStyles.scss');
    ReactDOM.render(<DefaultApp {...props} />, element);
}
