import React, {Suspense, useEffect, useState} from 'react';

const ComponentLoader = ({componentName, componentProps}) => {

	const [Component, setComponent] = useState(null);
	const [isLoaded, setIsLoaded] = useState(false);

	useEffect(() => {
		const importComponent = componentName => {
			const comp = React.lazy(() => import(`../../Components/${componentName}/index.js`));
			setComponent(comp);
			setIsLoaded(true);
		};
		importComponent(componentName);
	}, [componentName]);

	if(isLoaded && null !== Component) {
		return (
			<>
				<Suspense fallback={<div>Loading...</div>}>
					<Component {...componentProps}/>
				</Suspense>
			</>
		);
	} else {
		return null
	}
};

export default ComponentLoader;