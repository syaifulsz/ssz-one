module.exports = {
    watch: {
        css: {
            files: [
                'app/src/css/**/*.sass',
                '<%= config.project_dir %>/src/css/**/*.sass',
                'app/src/css/**/*.scss',
                '<%= config.project_dir %>/src/css/**/*.scss'
            ],
            tasks: [
                'css:<%= config.project_dir %>'
            ]
        },
        js: {
            files: [
                'app/src/js/**/*.es6',
                '<%= config.project_dir %>/src/js/**/*.es6'
            ],
            tasks: [
                'js:<%= config.project_dir %>'
            ]
        },
    }
};
