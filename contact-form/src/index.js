import React from 'react';
import ReactDOM from 'react-dom';
import App from './App';

const element = document.getElementById('app');

// todo: get data from url (trainingId, trainingName, date, location)
ReactDOM.render(<App title="MP06 Defensief rijden - thema 1 - C" date="02-03-2020" location="Alkmaar" trainingId="66" />, element);
