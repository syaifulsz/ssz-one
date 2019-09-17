module.exports = {
    css: [ 'sass', 'cssmin', 'notify:css' ],
    js: [ 'eslint', 'babel', 'uglify', 'notify:js' ],
    imagesFonts: [ 'copy:project', 'notify:copy' ],
    default: [ 'clean', 'css', 'js', 'imagesFonts', 'notify:ping' ]
};
