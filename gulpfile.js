/**
 * Gulpfile.
 *
 * Gulp for California Design System Structural integration for WordPress.
 * 
 * This Gulpfile is a modified version of WPGulp.
 * @tutorial https://github.com/ahmadawais/WPGulp
 * @author Ahmad Awais <https://twitter.com/MrAhmadAwais/>
 */

/**
 * Load WPGulp Configuration.
 *
 * TODO: Customize your project in the wpgulp.js file.
 */
const {
	success, 
	outputCSSDir,
	outputJSDir,
	availableColors,
	designSystemThemeDir,
	adminStyles,
	adminScripts,
	frontendStyles,
	frontendScripts,
	designSystemCSS,
	designSystemJS
} = require('./wpgulp.config.js');

/**
 * Load Plugins.
 *
 * Load gulp plugins and passing them semantic names.
 */
const {task,src, dest, parallel}  = require('gulp'); // Gulp of-course.

// Shell
const shell = require('gulp-shell')

// Monitoring related plugins.
const watch = require('gulp-watch');

// CSS related plugins.
const sass = require('gulp-sass')(require('node-sass')); // Gulp plugin for Sass compilation.

// JS related plugins.
const uglify = require('gulp-uglify-es').default; // Minifies JS files.

// HTML related plugins
const htmlbeautify = require('gulp-html-beautify'); // Beautify HTML/PHP files

// Utility related plugins.
const concat = require('gulp-concat'); // Concatenates files.
const lineec = require('gulp-line-ending-corrector'); // Consistent Line Endings for non UNIX systems. Gulp Plugin for Line Ending Corrector (A utility that makes sure your files have consistent line endings).
//const notify = require( 'gulp-notify' ); // Sends message notification to you.
const fs = require('fs'); // File System
const del = require('del'); // Delete plugin
var path = require('path');

var argv = require('yargs').argv;
var log = require('fancy-log');
var tap = require('gulp-tap');
const { color } = require('gulp-cli/lib/shared/cli-options.js');

task('monitor', function () {

	watch(['assets/**/*'], function (cb) {
		buildAllAssets();
	});
});

/**
 * Task to build CAWeb Theme Admin Styles
 */
task('admin-css', async function(){
	buildAdminStyles(true);
	buildAdminStyles(false);
});

/**
 * Task to build CAWeb FrontEnd Styles
 */
task('frontend-css', async function () {
	var colors = fs.readdirSync(designSystemThemeDir).filter(file => path.extname(file) === '.css');

	del(['css/cagov-*.css']);
	
	colors.forEach(function (v) {
		buildFrontEndStyles(false, v);
		buildFrontEndStyles(true, v);
	});

});

/**
 * Task to build CAWeb Theme Admin Scripts
 */
task('admin-js', async function () {

	del(['js/admin*.js']);

	buildAdminJS(true);
	buildAdminJS(false);

});

/**
 *	Task to build CAWeb Theme Frontend Scripts
 */
task('frontend-js', async function () {
	del(['js/cagov-*.js']);

	buildFrontendScripts(true);
	buildFrontendScripts(false);

});


/**
 * Task to help assist beautifying files
 */
task('beautify', async function () {
	var options = { indentSize: 2 };
	var src = ['**/*.php'];

	if (argv.hasOwnProperty('file')) {
		src = argv.file;
	}

	src(src, { base: './' })
		.pipe(htmlbeautify(options))
		.pipe(dest('./'));

});

/**
 * Task to build all CAWeb Theme Styles/Scripts
 */
task('build', async function () {
	del(['js/*.js', 'css/*.css']);

	parallel(
		'admin-css',
		'frontend-css',
		'admin-js',
		'frontend-js',
	)();
});


/**
 * Build CAWeb Theme Admin Styles
 * 
 * @param {*} min Whether to build file minified or not
 */
async function buildAdminStyles(min = false) {
	var buildOutputStyle = min ? 'compressed' : 'expanded';
	var minified = min ? '.min' : '';
	var t = minified ? ' Minified ] ' : ' ] ';
	t = '[ ' + success +' Design System Admin Styles' + t;

	if (adminStyles.length){
		src(adminStyles)
		.pipe(
			sass({
				outputStyle: buildOutputStyle,
			})
		)
		.pipe(lineec()) // Consistent Line Endings for non UNIX systems.
		.pipe(concat('admin' + minified + '.css')) // compiled file
		.pipe(dest(outputCSSDir))
		.pipe(tap(function (file) {
			log(t + path.basename(file.path) + ' was created successfully.');
		}));
	}

}

task('test', async function(){
	parallel(
		'admin-css',
	)();

})
/**
 * Build CAWeb Theme FrontEnd Styles
 * 
 * @param {*} min Whether to build file minified or not
 * @param {*} clr Design System Theme color
 */
async function buildFrontEndStyles(min = false, clr) {
	var buildOutputStyle = min ? 'compressed' : 'expanded';
	var minified = min ? '.min' : '';

	var coreCSS = [designSystemThemeDir + clr]
					.concat(designSystemCSS, frontendStyles);

	var color = availableColors[clr];
	var t = minified ? ' Minified ] ' : ' ] ';
				
	t = '[ ' + success + ' caGOV Design System ' + color + ' Colorscheme' + t;
	
	if (clr.length){
		// if minified add the .min
		clr = minified ? clr.replace('.css', '.min.css') : clr;
						
		var fileName = 'cagov-design-system-' + clr;
			
		src(coreCSS)
			.pipe(
				sass({
					outputStyle: buildOutputStyle,
				})
			)
			.pipe(lineec()) // Consistent Line Endings for non UNIX systems.
			.pipe(concat(fileName)) // compiled file
			.pipe(dest(outputCSSDir))
			.pipe(tap(function (file) {
				log(t + path.basename(fileName) + ' was created successfully.');
			}));
	}
}

/**
 * Build CAWeb Theme Admin Scripts
 * 
 * @param {*} min Whether to build file minified or not
 */
async function buildAdminJS(min = false) {
	var minified = min ? '.min' : '';
	var t = minified ? ' Minified ] ' : ' ] ';
	t = '[ ' + success + ' CAWeb Admin JavaScript' + t;

	if (adminScripts.length){
		let js = src(adminScripts)
		.pipe(lineec()) // Consistent Line Endings for non UNIX systems.
		.pipe(concat('admin' + minified + '.js')) // compiled file
		.pipe(tap(function (file) {
			log(t + path.basename(file.path) + ' was created successfully.');
		}))


		if (min) {
			js = js.pipe(uglify());
		}

		return js.pipe(dest(outputJSDir));
		
	}

}

/**
 * Build CAWeb Theme Frontend Scripts
 * 
 * @param {*} min Whether to build file minified or not
 */
async function buildFrontendScripts(min = false) {
	var minified = min ? '.min' : '';
	var coreJS = designSystemJS.concat(
			frontendScripts
		);

	var t = minified ? ' Minified ] ' : ' ] ';

	t = '[ ' + success + ' caGov Design System JavaScript' + t;

	if (coreJS.length){
		let js = src(coreJS)
		.pipe(lineec()) // Consistent Line Endings for non UNIX systems.
		.pipe(concat('cagov-design-system' + minified + '.js')) // compiled file
		.pipe(tap(function (file) {
			log(t + path.basename(file.path) + ' was created successfully.');
		}));

		if (min) {
			js = js.pipe(uglify());
		}

		js.pipe(dest(outputJSDir));
	}
}
