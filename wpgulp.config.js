/**
 * WPGulp Configuration File
 *
 * 1. Edit the variables as per your project requirements.
 * 2. In paths you can add <<glob or array of globs>>.
 *
 * @package WPGulp
 */


module.exports = {
	success: '✅',
	outputCSSDir: './css/',
	outputJSDir: './js/',
	availableColors: {
		'cagov.css' : 'CAGov',
		'cannabis.css' : 'Cannabis',
		'drought.css' : 'Drought',
	},
	designSystemThemeDir:  'node_modules/@cagov/ds-base-css/dist/themes/',
	adminStyles:[ // WP Backend Admin Styles
		'assets/scss/admin.scss'
	],
	adminScripts: [ // WP Backend Admin JS
	], 
	frontendStyles: [ // Frontend CSS
	],
	frontendScripts: [ // Common JS 
	], 
	designSystemCSS: [ // Design System Components CSS
		'node_modules/@cagov/*/src/index.scss',
	], 
	designSystemJS: [ // Design Components JS
		'node_modules/@cagov/*/dist/index.js',
	],
};