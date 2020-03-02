import React from 'react';
import ReactDOM from 'react-dom';
import App from './App';

const element = document.getElementById('app');

// Rendered in iframe so we take the location of the parent.
const urlParams = new URLSearchParams(window.parent.location.search);

const props = {
  title: urlParams.get('trainingName') || 'Training onbekend',
  date: urlParams.get('date') || 'Datum onbekend',
  location: urlParams.get('location') || 'Locatie onbekend',
  trainingId: urlParams.get('trainingId') || 'Training onbekend',
};

ReactDOM.render(<App {...props} />, element);

/*

const name = document.currentScript.dataset.name || 'app';
const dataNode = document.querySelector(`[data-props=${name}]`);
const externalProps = dataNode ? JSON.parse(dataNode.textContent) : {};

const props = {
  title: 'MP06 Defensief rijden - thema 1 - C',
  date: '02-03-2020',
  location: 'Alkmaar',
  trainingId: 66,
  ...externalProps,
};
 */

/**
 * Html snippet
 * `type="application/json"` is required to stop browsers frm trtying to execute the plain JSON
 */
/*
    <script src="path/to/bundle.js" data-name="BundleName" crossorigin />
    <script type="application/json" data-props="BundleName">
      {
        "title": "Nieuwe title",
        "date": "01-01-2021",
      }
    </script>
*/
