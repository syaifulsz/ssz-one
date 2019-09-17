module.exports = {
    uglify: {
        options: {
            mangle: false
        },
        main: {
            files: {
                '<%= config.project_dir %>/public/assets/js/vendors.min.js': [
                    'node_modules/jquery/dist/jquery.min.js',
                    'node_modules/bootstrap/dist/js/bootstrap.js',
                ],
                '<%= config.project_dir %>/public/assets/js/common.min.js': [
                    '<%= config.project_dir %>/src/js/common-compiled.js',
                ]
            }
        },
    }
};
