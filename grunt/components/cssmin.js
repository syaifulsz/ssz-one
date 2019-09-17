module.exports = {
    cssmin: {
        options: {
            mergeIntoShorthands: false,
            roundingPrecision: -1
        },
        common: {
            files: {
                '<%= config.project_dir %>/public/assets/css/common.min.css': [
                    'node_modules/bootstrap/dist/css/bootstrap.min.css',
                    '<%= config.project_dir %>/src/css/sass/common.css',
                ]
            }
        },
    }
};
