export const config = {
	"styleLoader": "BootstrapLoader",
	"card": {
		"headerText": "Inschrijving voor Training 26-12",
		"classes": "m-3",
	},
	"cardTitle": {
		"classes": "text-left m-0",
	},
	"form": {
		"classes": "text-left",
		"inputs": [
			{
				"type": "TextInput",
				"label": "Voornaam",
				"name": "firstName",
				"required": false,
				"wrapperClasses": "m-0"
			},
			{
				"type": "TextInput",
				"label": "Achternaam",
				"name": "lastName",
				"required": false,
				"wrapperClasses": "m-0"
			},
			{
				"type": "TextInput",
				"label": "GSM nummer",
				"name": "mobilePhone",
				"required": false,
				"wrapperClasses": "m-0"
			},
			{
				"type": "TextInput",
				"label": "E-mail",
				"name": "email",
				"required": false,
				"wrapperClasses": "m-0"
			},
			{
				"type": "TextInput",
				"label": "Geboorteplaats",
				"name": "placeOfBirth",
				"required": false,
				"wrapperClasses": "m-0"
			},
			{
				"type": "DateInput",
				"label": "Geboortedatum",
				"name": "dateOfBirth",
				"required": false,
				"wrapperClasses": "m-0",
				"labelClasses": "text-left"
			},
			{
				"type": "TextInput",
				"label": "Rijksregisternummer",
				"name": "nationalRegistryNumber",
				"required": false,
				"wrapperClasses": "m-0"
			},
			{
				"type": "TextInput",
				"label": "Identiteitskaarnummer",
				"name": "idCardNumber",
				"required": false,
				"wrapperClasses": "m-0"
			},
			{
				"type": "DateInput",
				"label": "Datum behalen rijbewijs C",
				"name": "dateLicenceC",
				"required": false,
				"wrapperClasses": "m-0",
				"labelClasses": "text-left"
			},
			{
				"type": "DateInput",
				"label": "Datum code 95",
				"name": "dateCode95",
				"required": false,
				"wrapperClasses": "m-0",
				"labelClasses": "text-left"
			},
			{
				"type": "DateInput",
				"label": "Datum medische schifting",
				"name": "dateMedical",
				"required": false,
				"wrapperClasses": "m-0",
				"labelClasses": "text-left"
			},
			{
				"type": "CheckBox",
				"label": "Akkoord met de privacyverklaring en algemene voorwaarden",
				"name": "daccord",
				"required": false
			},
		],
		"submitButton" : {
			//TODO: This should not be changed obviously
			"url": "https://cloud-dev.dation.nl:269/wp-json/dationwoocommerce/v1/submit/lead",
			"inputClasses" : "btn btn-primary float-right",
			"text" : "Verzenden",
		}
	}
};