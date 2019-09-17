const sass = require( 'node-sass' );
const path = require( 'path' );
const rootPath = path.resolve( __dirname, '../../node_modules' );

console.log( rootPath );

module.exports = {
    sass: {
        options: {
            includePaths: [ rootPath, 'node_modules', '.' ],
            implementation: sass,
            sourceMap: true
        },
        common: {
            files: {
                '<%= config.project_dir %>/src/css/sass/common.css': '<%= config.project_dir %>/src/css/sass/common.sass'
            }
        },
    }
};
