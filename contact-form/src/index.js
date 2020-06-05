import React from 'react';
import ReactDOM from 'react-dom';
import App from './App';
import LeadFormApp from "./LeadFormApp";

const element = document.getElementById('app');

// Rendered in iframe so we take the location of the parent.
const urlParams = new URLSearchParams(window.parent.location.search);

const props = {
  title: urlParams.get('trainingName') || 'Training onbekend',
  date: urlParams.get('date') || 'Datum onbekend',
  location: urlParams.get('location') || 'Locatie onbekend',
  trainingId: urlParams.get('trainingId') || 'Training onbekend',
};
// if(urlParams.get('belgianForm') === '1') {
  ReactDOM.render(<App {...props} />, element);
// } else {
//   ReactDOM.render(<LeadFormApp {...props} />, element);
// }
