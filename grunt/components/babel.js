module.exports = {
    babel: {
        options: {
            sourceMap: false,
            presets: ['env']
        },
        dist: {
            files: [
                {
                    expand: true,
                    cwd: 'app/src/js/',
                    src: [
                        '*.es6',
                        '**/*.es6'
                    ],
                    dest: 'app/src/js',
                    ext: '-compiled.js'
                },
                {
                    expand: true,
                    cwd: '<%= config.project_dir %>/src/js/',
                    src: [
                        '*.es6',
                        '**/*.es6'
                    ],
                    dest: '<%= config.project_dir %>/src/js',
                    ext: '-compiled.js'
                }
            ]
        },
    }
};
