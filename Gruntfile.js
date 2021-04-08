module.exports = function (grunt) {
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        bowercopy: {
            options: {
                srcPrefix: 'node_modules',
                destPrefix: 'web/libs'
            },
            scripts: {
                files: {
                    'js/jquery.min.js': 'jquery/dist/jquery.min.js',
                    'js/bootstrap.min.js': 'bootstrap/dist/js/bootstrap.bundle.min.js',
                }
            },
            stylesheets: {
                files: {
                    'css/bootstrap.css': 'bootstrap/dist/css/bootstrap.css',
                    'css/font-awesome.css': 'font-awesome/css/font-awesome.css'
                }
            },
            scss: {
                files: {
                    'css/bootstrap.css': 'bootstrap/dist/css/bootstrap.css',
                    'css/font-awesome.css': 'font-awesome/css/font-awesome.css'
                }
            },
            fonts: {
                files: {
                    '../assets/fonts': 'font-awesome/fonts'
                }
            }
        },
        compass: {                  // Task
            dist: {                   // Target
              options: {              // Target options
                sassDir: 'web/libs/scss',
                cssDir:  'web/libs/css',
                environment: 'production'
              }
            },
            dev: {                    // Another target
              options: {
                sassDir: 'web/libs/scss',
                cssDir:  'web/libs/css'
              }
            }
          },
        cssmin : {
            bootstrap:{
                src: 'web/libs/css/bootstrap.css',
                dest: 'web/libs/css/bootstrap.min.css'
            },
            "font-awesome":{
                src: 'web/libs/css/font-awesome.css',
                dest: 'web/libs/css/font-awesome.min.css'
            },
            app:{
                src: 'web/libs/css/app.css',
                dest: 'web/libs/css/app.min.css'
            },
            red:{
                src: 'web/libs/css/red.css',
                dest: 'web/libs/css/red.min.css'
            }
        },
        uglify : {
            js: {
                files: {
                    'web/libs/js/app.min.js': ['web/libs/js/app.js']
                }
            }
        },
        concat: {
            options: {
                stripBanners: true
            },
            css_libs: {
                src: [
                    'web/libs/css/bootstrap.min.css',
                    'web/libs/css/font-awesome.min.css',
                    'web/libs/css/app.min.css'
                ],
                dest: 'web/assets/css/app.min.css'
            },
            css_libs: {
                src: [
                    'web/libs/css/red.min.css',
                ],
                dest: 'web/assets/css/red.min.css'
            },
            js_main : {
                src : [
                    'web/libs/js/jquery.min.js',
                    'web/libs/js/bootstrap.min.js',
                    'web/libs/js/app.min.js',
                ],
                dest: 'web/assets/js/app.js'
            },
        },
        watch: {
            files: ['web/libs/scss/*'],
            tasks: ['compass', 'cssmin', 'concat', 'uglify']
//            files: ['web/libs/js/form/*'],
//            tasks: ['uglify', 'concat']
         }
    });

    grunt.loadNpmTasks('grunt-bowercopy');
    grunt.loadNpmTasks('grunt-contrib-compass');
    grunt.loadNpmTasks('grunt-contrib-concat');
    grunt.loadNpmTasks('grunt-contrib-cssmin');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-watch');

    grunt.registerTask('default', ['bowercopy', 'compass', 'cssmin', 'uglify', 'concat']);
};
