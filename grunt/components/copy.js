module.exports = {
    clean: {
        assets: ['<%= config.project_dir %>/public/assets/*'],
    },
    copy: {
        appImages: {
            expand: true,
            cwd: 'app/src/images',
            src: '**',
            dest: '<%= config.project_dir %>/public/assets/images/app/',
        },
        project: {
            expand: true,
            cwd: '<%= config.project_dir %>/src/images',
            src: '**',
            dest: '<%= config.project_dir %>/public/assets/images/',
        }
    }
};
