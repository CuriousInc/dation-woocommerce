/* disable a11y plugin from airbnb-config */
const a11yOff = Object.keys(require('eslint-plugin-jsx-a11y').rules)
	.reduce((acc, rule) => { acc[`jsx-a11y/${rule}`] = 'off'; return acc }, {})

module.exports = {
  "extends": ["react-app", "airbnb"],
  "parserOptions": {
    "ecmaVersion": 8
  },
  "env": {
    "browser": true,
    "jest": true
  },
  "rules": {
    ...a11yOff,
    "class-methods-use-this": ["off"],
    "comma-dangle": ["error", "always-multiline"],
    "complexity": ["warn", 8],
    "max-depth": ["warn", 3],
    "no-underscore-dangle": ["error", { "allow": ["__INITIAL_STATE__"] }],
    "no-unused-expressions": ["error", { "allowShortCircuit": true, "allowTernary": true }],
    "no-plusplus": ["error", { "allowForLoopAfterthoughts": true }],
    "quotes": ["warn", "single"],
    "import/imports-first": ["error", "absolute-first"],
    "import/newline-after-import": ["error"],
    // This rule conflicts exporting connected components
    // https://github.com/benmosher/eslint-plugin-import/issues/544#issuecomment-244976007
    "import/no-named-as-default": ["off"],
    "import/no-extraneous-dependencies": ["error", {
      "devDependencies": [
        "setupTests.js",
        "**/__tests__/*",
        "**/__mocks__/*"
      ]
    }],
    "react/jsx-filename-extension": ["error", { "extensions": [".js", ".jsx"] }],
    "react/jsx-one-expression-per-line": ["off"],
    "react/jsx-props-no-spreading": ["off"],
    "react/forbid-prop-types": ["error", {
      "forbid": ["any"]
    }],
    "react/prop-types": [ "warn", {
        "skipUndeclared": true
    }],
    "jsx-quotes": ["error", "prefer-double"]
  }
}
