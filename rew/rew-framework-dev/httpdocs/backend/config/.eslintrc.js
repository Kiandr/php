module.exports = {
    "env": {
        "browser": true,
        "jquery": true,
        "amd": true,
        "es6": true
    },
    "plugins": [
        "react",
        "html"
    ],
    "extends": [
        "eslint:recommended",
        "plugin:react/all"
    ],
    "parser": "babel-eslint",
    "parserOptions": {
        "sourceType": "module"
    },
    "rules": {
        "indent": [
            "error",
            4
        ],
        "linebreak-style": [
            "error",
            "unix"
        ],
        "quotes": [
            "error",
            "single"
        ],
        "semi": [
            "error",
            "always"
        ],
        "no-useless-escape": 0,
        "react/forbid-component-props": 0,
        "react/void-dom-elements-no-children": 0,
        "react/jsx-closing-bracket-location": 0,
        "react/jsx-max-props-per-line": 0,
        "react/jsx-wrap-multilines": 0,
        "react/react-in-jsx-scope": 0,
        "react/sort-prop-types": 0,
        "react/no-set-state": 0,
        "react/jsx-sort-props": 0,
        "react/prefer-stateless-function": ["warn", {ignorePureComponents: true}],
        "react/require-optimization": [2, {allowDecorators: ['pureRender']}],
        "react/jsx-no-bind": [2, {"allowArrowFunctions": true}],
        "react/jsx-boolean-value": 0
    }
};
