import React from 'react';
import './App.css';
import Form from './Components/Form';
import { updateConfigClasses, injectBootstrapCss } from './Loaders/StyleLoader';
import { config } from './testConfig';

function App() {
	const updatedConfig = updateConfigClasses(config);
	const {card, form} = updatedConfig;

	injectBootstrapCss();

	return (
		<div className="App">
			<div className="container">
				<div className={updatedConfig.card.classes}>
					<div className={updatedConfig.cardHeader.classes}>
						<div className={updatedConfig.cardTitle.classes}>{card.headerText}</div>
					</div>
					<div className={updatedConfig.cardBody.classes}>
						<Form config={form}/>
					</div>
				</div>
			</div>
		</div>
	);
}

export default App;
