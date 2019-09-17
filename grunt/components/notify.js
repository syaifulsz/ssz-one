module.exports = {
    notify: {
        ping: {
            options:{
                title: '<%= config.project_name %>',
                message: 'PONG!'
            }
        },
        watch: {
            options:{
                title: '<%= config.project_name %>',
                message: 'Watching...'
            }
        },
        css: {
            options:{
                title: '<%= config.project_name %>',
                message: 'CSS Ready!'
            }
        },
        js: {
            options:{
                title: '<%= config.project_name %>',
                message: 'JS Ready!'
            }
        },
        copy: {
            options:{
                title: '<%= config.project_name %>',
                message: 'Images Copied!'
            }
        }
    }
};
