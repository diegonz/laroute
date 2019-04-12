module.exports = function (grunt) {
    grunt.initConfig({
        pkg : grunt.file.readJSON('package.json'),

        uglify : {
            build : {
                files : {
                    'resources/js/templates/laroute.min.js' : 'resources/js/templates/laroute.js'
                }
            }
        }
    });

    grunt.loadNpmTasks('grunt-contrib-uglify-es');

    grunt.registerTask('default', ['uglify']);
};
