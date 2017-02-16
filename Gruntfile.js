'use strict';
module.exports = function (grunt) {

    // Load grunt tasks automatically
    require('load-grunt-tasks')(grunt);

    // MODULES

    grunt.config('clean', {
        basic: [
            'assets/**/*.css', 'assets/**/*.css.map', '!assets/**/custom.css', '!assets/**/custom-pdf.css', // CSS
            'assets/default/js/*.js', '!assets/default/js/scripts.js', '!assets/default/js/jquery-ui.js', // JS
            'assets/default/fonts/*', '!assets/default/fonts/.gitignore' // Fonts
        ],
        build: ['assets/default/js/dependencies.js', 'assets/default/js/legacy.js']
    });

    grunt.config('sass', {
        dev: {
            options: {
                outputStyle: 'extended',
                sourceMap: true
            },
            files: grunt.file.expandMapping(['assets/**/sass/*.scss'], 'css', {
                rename: function (dest, matched) {
                    return matched.replace(/\/sass\//, '/' + dest + '/').replace(/\.scss$/, '.css');
                }
            })
        },
        build: {
            options: {
                outputStyle: 'compressed'
            },
            files: grunt.file.expandMapping(['assets/**/sass/*.scss'], 'css', {
                rename: function (dest, matched) {
                    return matched.replace(/\/sass\//, '/' + dest + '/').replace(/\.scss$/, '.css');
                }
            })
        }
    });

    grunt.config('postcss', {
        dev: {
            options: {
                map: true,
                processors: [
                    require('autoprefixer')({
                        browsers: 'last 3 version'
                    })
                ]
            },
            src: [
                'assets/**/css/*.css',
                '!assets/**/css/custom.css',
                '!assets/**/css/custom-pdf.css'
            ]
        },
        build: {
            options: {
                map: false,
                processors: [
                    require('autoprefixer')({
                        browsers: 'last 3 version'
                    })
                ]
            },
            src: [
                'assets/**/css/*.css',
                '!assets/**/css/custom.css',
                '!assets/**/css/custom-pdf.css'
            ]
        }
    });

    grunt.config('concat', {
        legacy: {
            src: [
                'node_modules/html5shiv/dist/html5shiv.js'
            ],
            dest: 'assets/default/js/legacy.js'
        },
        dependencies: {
            src: [
                'node_modules/jquery/dist/jquery.js',
                'assets/default/js/jquery-ui.js',
                'node_modules/bootstrap-sass/assets/javascripts/bootstrap.js',
                'node_modules/bootstrap-datepicker/js/bootstrap-datepicker.js',
                'node_modules/select2/dist/js/select2.js',
                'node_modules/dropzone/dist/dropzone.js'
            ],
            dest: 'assets/default/js/dependencies.js'
        }
    });

    grunt.config('uglify', {
        build: {
            files: {
                'assets/default/js/legacy.min.js': ['assets/default/js/legacy.js'],
                'assets/default/js/dependencies.min.js': ['assets/default/js/dependencies.js'],
                'assets/default/js/scripts.min.js': ['assets/default/js/scripts.js']
            }
        }
    });

    grunt.config('copy', {
        datepickerlocale: {
            expand: true,
            flatten: true,
            src: ['node_modules/bootstrap-datepicker/js/locales/**'],
            dest: 'assets/default/js/locales/',
            filter: 'isFile'
        },
        fontawesome: {
            expand: true,
            flatten: true,
            src: ['node_modules/font-awesome/fonts/*'],
            dest: 'assets/default/fonts'
        },
        devjs: {
            files: [{
                cwd: 'assets/default/js/',
                src: [
                    '*.js', '!jquery-ui.js'
                ],
                dest: 'assets/default/js/',
                expand: true,
                rename: function(dest, src) {
                    return (dest + src).replace('.js', '.min.js');
                }
            }]
        }
    });

    grunt.config('watch', {
        sass: {
            files: "assets/**/*.scss",
            tasks: ['sass:dev', 'postcss:dev']
        },
        js: {
            files: "assets/default/js/scripts.js",
            tasks: ['sass:dev']
        }
    });

    // TASKS

    grunt.registerTask('default', 'dev');

    grunt.registerTask('dev', [
        'clean:basic',
        'sass:dev',
        'postcss:dev',
        'concat:legacy',
        'concat:dependencies',
        'copy:datepickerlocale',
        'copy:fontawesome',
        'copy:devjs',
        'watch'
    ]);

    grunt.registerTask('build', [
        'clean:basic',
        'sass:build',
        'postcss:build',
        'concat:legacy',
        'concat:dependencies',
        'uglify:build',
        'clean:build',
        'copy:datepickerlocale',
        'copy:fontawesome'
    ]);
};
