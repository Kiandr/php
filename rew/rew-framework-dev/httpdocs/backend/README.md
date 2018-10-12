# REW Backend

### Prerequisites

 - [NodeJS](https://nodejs.org/) 0.10.36+ and [NPM](https://www.npmjs.com/) 4.0.5+

### Building CSS styles
 
[PostCSS](https://github.com/postcss/postcss) is used to transform the backend's CSS style.
 
The application's styles are stored in [`assets/styles/app.css`](assets/styles/app.css) and are built using the commands:

```
npm run build-css # build application's stylesheet: `build/css/app.css`
npm run watch-css # watch for changes to `assets/styles` and rebuild
```

Configuration for these [Gulp](https://github.com/gulpjs/gulp) tasks are located in: [`config/gulpfile.babel.js`](config/gulpfile.babel.js)

### Building JavaScript files
[webpack](https://github.com/webpack/webpack) is used to bundle the application's scripts for the browser.

[webpack's configuration options](https://webpack.js.org/configuration/) are loaded from [`config/webpack.config.babel.js`](config/webpack.config.babel.js).

##### Development Build

When changes are made to JS assets, the sources need to be updated using the command:

```
npm run build-js 
```

Watch for changes to JS files and re-build as needed using: `npm run watch-js`

##### Production Build

You may have noticed that the bundles are pretty hefty. This may result in slow JS performance.

In a production environment, we'll want to enable minify, compress and disable debugging.

```
npm run ship-js
```

#### Writing JavaScript Modules

[Babel](https://github.com/babel/babel) allows us to write tomorrow's JavaScript today. :shipit:
 [Learn ECMAScript 2015](https://babeljs.io/learn-es2015/) and become a JS Ninja.

Our JS files use the [ES6 module syntax](http://www.2ality.com/2014/09/es6-modules-final.html), keeping code compact and declarative for easy understanding and re-use.

##### Learn more about ES6 modules & features:

* https://hacks.mozilla.org/2015/08/es6-in-depth-modules/
* https://github.com/lukehoban/es6features

#### ESLint

[ESLint](http://eslint.org/) is used to find problematic JavaScript code within [`assets/scripts`](assets/scripts) by running the command:  `npm run eslint`

Rules are configured in [config/.eslintrc.js](config/.eslintrc.js) and extends the default [`"eslint:recommended"`](http://eslint.org/docs/rules/) ruleset.
Excluded paths are defined in [config/.eslintignore](config/.eslintignore).

**Disabling ESLint rules**

It may be desired to disable linting rules on a per-line basis and can be done by adding: `// eslint-disable-line <name-of-rule>`

For example, using `console.log` will throw an ESLint error because of the [`no-console`](http://eslint.org/docs/rules/no-console) rule, but it can be disabled:

```js
console.log('error');
console.log('valid'); // eslint-disable-line no-console
```

#### JavaScript Bundles
* Page scripts are loaded based on the URL's path. For example, `/backend/leads/` will load:
  * `build/js/pages/leads.js` or `build/js/pages/leads/default.js`
  * This is the same structure used for the page controllers and templates.

![](https://cloud.githubusercontent.com/assets/294947/25349766/cd385fd4-28d7-11e7-91b3-92791d67b28c.png)
