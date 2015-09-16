'use strict';
module.exports = function (grunt) {

    // load grunt tasks
    require('load-grunt-tasks')(grunt);

    grunt.initConfig({

        config: {
            src: 'assets/InvoicePlane',
            dest: 'assets/InvoicePlane'
        },
        jshint: {
            options: {
                jshintrc: '.jshintrc'
            },
            all: [
                'Gruntfile.js',
                'assets/core/js/*.js'
            ]
        },
        sass: {
            dist: {
                options: {
                    style: 'compressed',
                    compass: true,
                    sourcemap: false
                },
                files: {
                    'assets/InvoicePlane/css/app.min.css': 'assets/InvoicePlane/app.scss',
                    'assets/InvoicePlane/css/basic.min.css': 'assets/InvoicePlane/basic.scss',
                    'assets/InvoicePlane/css/monospace.min.css': 'assets/InvoicePlane/monospace.scss',
                    'assets/InvoicePlane/css/reports.min.css': 'assets/InvoicePlane/reports.scss',
                    'assets/InvoicePlane/css/template.min.css': 'assets/InvoicePlane/templates.scss'
                }
            }
        },
        uglify: {
            dist: {
                files: {
                    'assets/core/js/dependencies.min.js': [
                        'assets/vendor/jquery/dist/jquery.js',
                        'assets/vendor/bootstrap/dist/js/bootstrap.js',
                        'assets/vendor/jqueryui/ui/core.js',
                        'assets/vendor/jqueryui/ui/widget.js',
                        'assets/vendor/jqueryui/ui/mouse.js',
                        'assets/vendor/jqueryui/ui/position.js',
                        'assets/vendor/jqueryui/ui/draggable.js',
                        'assets/vendor/jqueryui/ui/droppable.js',
                        'assets/vendor/jqueryui/ui/resizable.js',
                        'assets/vendor/jqueryui/ui/selectable.js',
                        'assets/vendor/jqueryui/ui/sortable.js',
                        'assets/vendor/bootstrap-datepicker/dist/js/bootstrap-datepicker.js',
                        'assets/vendor/dropzone/dist/dropzone.js',
                        'assets/vendor/select2/dist/js/select2.js'
                    ],
                    'assets/core/js/app.min.js': [
                        'assets/core/js/app.js'
                    ]
                },
                options: {
                    sourceMap: 'assets/core/js/app.min.js.map',
                    sourceMappingURL: '/assets/core/js/app.min.js.map'
                }
            }
        },
        watch: {
            sass: {
                files: [
                    'assets/InvoicePlane/*.scss'
                ],
                tasks: ['sass']
            },
            js: {
                files: [
                    '<%= jshint.all %>',
                    'assets/core/js/*.js'
                ],
                tasks: ['jshint', 'uglify']
            }
        },
        clean: {
            dist: [
                'assets/InvoicePlane/css/*.min.css',
                'assets/InvoicePlane/css/*.min.css.map',
                'assets/core/js/app.min.js'
            ]
        }
    });

    // Register tasks
    grunt.registerTask('built', [
        'clean',
        'sass',
        'uglify'
    ]);
    grunt.registerTask('dev', [
        'built',
        'watch'
    ]);

};
