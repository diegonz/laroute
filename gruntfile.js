module.exports = function (grunt) {
    grunt.initConfig({
        pkg : grunt.file.readJSON('package.json'),

        uglify : {
            build : {
                files : {
                    'src/templates/laroute.min.js' : 'src/templates/laroute.js'
                }
            }
        }
    });

    grunt.loadNpmTasks('grunt-contrib-uglify-es');

    grunt.registerTask('default', ['uglify']);
};
