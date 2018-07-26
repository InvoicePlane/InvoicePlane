'use strict';
module.exports = function (grunt) {

  // Load grunt tasks automatically
  require('load-grunt-tasks')(grunt);

  // MODULES

  grunt.config('clean', {
    styles: [
      'assets/dist/*.css', 'assets/dist/*.css.map' // CSS
    ],
    js: [
      'assets/dist/*.js', 'assets/dist/*.js.map' // Javascript
    ],
    fonts: [
      'assets/dist/fonts/**/*.*', '!assets/dist/fonts/.gitignore' // Font Awesome
    ],
    third_party: [
      'assets/dist/bs-datepicker/**/*.*', '!assets/dist/bs-datepicker/.gitignore', // Bootstrap Datepicker
      'assets/dist/chosen-js/**/*.*', '!assets/dist/chosen-js/.gitignore', // Chosen JS
      'assets/dist/daterangepicker/**/*.*', '!assets/dist/daterangepicker/.gitignore', // Chosen JS
      'assets/dist/typeahead/**/*.*', '!assets/dist/typeahead/.gitignore', // Typeahead JS
    ]
  });

  grunt.config('sass', {
    dev: {
      options: {
        outputStyle: 'extended',
        sourceMap: true
      },
      files: {
        'assets/dist/app.css': 'resources/assets/sass/app.scss',
        'assets/dist/adminlte/css/skins/skin-invoiceplane.min.css': 'resources/assets/sass/skin-invoiceplane.scss'
      }
    },
    build: {
      options: {
        outputStyle: 'compressed',
        sourceMap: false
      },
      files: {
        'assets/dist/app.css': 'resources/assets/sass/app.scss',
        'assets/dist/adminlte/css/skins/skin-invoiceplane.min.css': 'resources/assets/sass/skin-invoiceplane.scss'
      }
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
        'assets/dist/*.css'
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
        'assets/dist/*.css'
      ]
    }
  });

  grunt.config('concat', {
    js_dependencies: {
      src: [
        'node_modules/jquery/dist/jquery.js',
        'node_modules/jquery-ui-dist/jquery-ui.js',
        'node_modules/bootstrap/dist/js/bootstrap.bundle.js',
        'node_modules/autosize/dist/autosize.js',
        'node_modules/moment/moment.js',
        'node_modules/bootstrap-notify/bootstrap-notify.js',
        'node_modules/jquery-slimscroll/jquery.slimscroll.js'
      ],
      dest: 'assets/dist/dependencies.js'
    }
  });

  grunt.config('uglify', {
    js_app: {
      files: {
        'assets/dist/app.js': ['assets/dist/dependencies.js']
      }
    },
    js_dependencies: {
      files: {
        'assets/dist/dependencies.js': ['resources/assets/js/app.js']
      }
    }
  });

  grunt.config('copy', {
    app_js: {
      src: ['resources/assets/js/app.js'],
      dest: 'assets/dist/app.js'
    },
    fontawesome: {
      expand: true,
      flatten: true,
      src: ['node_modules/font-awesome/fonts/*'],
      dest: 'assets/dist/fonts/'
    },
    chosen_js: {
      expand: true,
      cwd: 'node_modules/chosen-js',
      src: ['chosen.css', 'chosen.jquery.js', '*.png'],
      dest: 'assets/dist/chosen-js/'
    },
    bs_datepicker: {
      expand: true,
      cwd: 'node_modules/bootstrap-datepicker/dist',
      src: ['locales/*.js', 'js/bootstrap-datepicker.min.js', 'css/bootstrap-datepicker3.min.css'],
      dest: 'assets/dist/bs-datepicker/'
    },
    daterangepicker: {
      expand: true,
      cwd: 'node_modules/daterangepicker/',
      src: ['daterangepicker.css', 'daterangepicker.js'],
      dest: 'assets/dist/daterangepicker/'
    },
    typeahead: {
      expand: true,
      cwd: 'node_modules/typeahead.js/dist/',
      src: ['typeahead.bundle.min.js'],
      dest: 'assets/dist/typeahead/'
    }
  });

  grunt.config('watch', {
    sass: {
      files: 'resources/assets/sass/**/*.scss',
      tasks: ['sass:dev', 'postcss:dev']
    }
  });

  // TASKS

  grunt.registerTask('default', 'build');

  grunt.registerTask('dev-build', [
    'clean:styles',
    'clean:js',
    'clean:fonts',
    'clean:third_party',
    'sass:dev',
    'postcss:dev',
    'concat:js_dependencies',
    'copy:app_js',
    'copy:fontawesome',
    'copy:chosen_js',
    'copy:bs_datepicker',
    'copy:daterangepicker',
    'copy:typeahead'
  ]);

  grunt.registerTask('dev', [
    'dev-build',
    'watch'
  ]);

  grunt.registerTask('build', [
    'clean:styles',
    'clean:js',
    'clean:fonts',
    'clean:third_party',
    'sass:build',
    'postcss:build',
    'concat:js_dependencies',
    'uglify:js_dependencies',
    'uglify:js_app',
    'copy:fontawesome',
    'copy:chosen_js',
    'copy:bs_datepicker',
    'copy:daterangepicker',
    'copy:typeahead'
  ]);
};
