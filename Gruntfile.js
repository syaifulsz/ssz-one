module.exports = function(grunt) {

    const project = grunt.option('project') || 'ssz1portal';
    console.log('project=' + project);

    grunt.initConfig(require( './grunt/config/'+ project +'/gruntfile.js' ));

    grunt.loadNpmTasks('grunt-contrib-jshint');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-cssmin');
    grunt.loadNpmTasks('grunt-sass');
    grunt.loadNpmTasks('grunt-babel');
    grunt.loadNpmTasks('grunt-notify');
    grunt.loadNpmTasks('grunt-eslint');
    grunt.loadNpmTasks('grunt-contrib-copy');
    grunt.loadNpmTasks('grunt-contrib-clean');

    const tasks = require( './grunt/config/'+ project +'/tasks.js' );
    for ( key in tasks ) {
        grunt.registerTask( key, tasks[ key ] )
    }
};
