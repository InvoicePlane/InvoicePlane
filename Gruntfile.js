'use strict';
module.exports = function (grunt) {

    // Load grunt tasks automatically
    require('load-grunt-tasks')(grunt);

    // MODULES

    grunt.config('clean', {
        basic: [
            'assets/**/*.css', 'assets/**/*.css.map', '!assets/core/css/custom.css', '!assets/core/css/custom-pdf.css', // CSS
            'assets/core/js/*.js', '!assets/core/js/scripts.js', '!assets/core/js/jquery-ui.js', // JS
            'assets/core/fonts/*', '!assets/core/fonts/.gitignore' // Fonts
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
                '!assets/core/css/custom.css',
                '!assets/core/css/custom-pdf.css'
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
                '!assets/core/css/custom.css',
                '!assets/core/css/custom-pdf.css'
            ]
        }
    });

    grunt.config('concat', {
        legacy: {
            src: [
                'node_modules/html5shiv/dist/html5shiv.js'
            ],
            dest: 'assets/core/js/legacy.js'
        },
        dependencies: {
            src: [
                'node_modules/jquery/dist/jquery.js',
                'node_modules/js-cookie/src/js.cookie.js',
                'assets/core/js/jquery-ui.js',
                'node_modules/bootstrap-sass/assets/javascripts/bootstrap.js',
                'node_modules/bootstrap-datepicker/js/bootstrap-datepicker.js',
                'node_modules/select2/dist/js/select2.full.js',
                'node_modules/dropzone/dist/dropzone.js',
                'node_modules/clipboard/dist/clipboard.js'
            ],
            dest: 'assets/core/js/dependencies.js'
        },
        zxcvbn: {
            src: [
                'node_modules/zxcvbn/dist/zxcvbn.js'
            ],
            dest: 'assets/core/js/zxcvbn.js'
        }
    });

    grunt.config('uglify', {
        build: {
            files: {
                'assets/core/js/legacy.min.js': ['assets/core/js/legacy.js'],
                'assets/core/js/dependencies.min.js': ['assets/core/js/dependencies.js'],
                'assets/core/js/scripts.min.js': ['assets/core/js/scripts.js']
            }
        }
    });

    grunt.config('copy', {
        datepickerlocale: {
            expand: true,
            flatten: true,
            src: ['node_modules/bootstrap-datepicker/js/locales/**'],
            dest: 'assets/core/js/locales/',
            filter: 'isFile'
        },
        fontawesome: {
            expand: true,
            flatten: true,
            src: ['node_modules/font-awesome/fonts/*'],
            dest: 'assets/core/fonts'
        },
        devjs: {
            files: [{
                cwd: 'assets/core/js/',
                src: [
                    '*.js', '!jquery-ui.js'
                ],
                dest: 'assets/core/js/',
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
            files: "assets/core/js/scripts.js",
            tasks: ['uglify']
        }
    });

    // TASKS

    grunt.registerTask('default', 'build');

    grunt.registerTask('dev-build', [
        'clean:basic',
        'sass:dev',
        'postcss:dev',
        'concat:legacy',
        'concat:dependencies',
        'concat:zxcvbn',
        'copy:datepickerlocale',
        'copy:fontawesome',
        'copy:devjs'
    ]);

    grunt.registerTask('dev', [
        'clean:basic',
        'sass:dev',
        'postcss:dev',
        'concat:legacy',
        'concat:dependencies',
        'concat:zxcvbn',
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
        'concat:zxcvbn',
        'uglify:build',
        'clean:build',
        'copy:datepickerlocale',
        'copy:fontawesome'
    ]);
};
