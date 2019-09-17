const config = require( './config.js' );
const component_notify = require( '../../components/notify.js' );
const component_eslint = require( '../../components/eslint.js' );
const component_watch = require( '../../components/watch.js' );
const component_babel = require( '../../components/babel.js' );
const component_clean = require( '../../components/clean.js' );
const component_sass = require( '../../components/sass.js' );
const component_cssmin = require( '../../components/cssmin.js' );
const component_uglify = require( '../../components/uglify.js' );
const component_copy = require( '../../components/copy.js' );

console.log( `Loading Grunt config for ${config.project_name}...` );

module.exports = {

    config: config,
    ...component_notify,
    ...component_sass,
    ...component_cssmin,
    ...component_eslint,
    ...component_watch,
    ...component_babel,
    ...component_uglify,
    ...component_clean,
    ...component_copy
};
