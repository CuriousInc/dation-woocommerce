import React from 'react';
import './App.css';
import Form from './Components/Form';
import BootstrapLoader, { updateConfigClasses } from './Loaders/StyleLoader';
import {config} from './testConfig';

function App() {
	const updatedConfig = updateConfigClasses(config);
	const {card, form} = updatedConfig;

	return (
		<div className="App">
			<BootstrapLoader>
				<div className={updatedConfig.card.classes}>
					<div className={updatedConfig.cardHeader.classes}>
						<div className={updatedConfig.cardTitle.classes}>{card.headerText}</div>
					</div>
					<div className={updatedConfig.cardBody.classes}>
						<Form config={form}/>
					</div>
				</div>
			</BootstrapLoader>
		</div>
	);
}

export default App;
