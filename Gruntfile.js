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
    ]
  });

  grunt.config('sass', {
    dev: {
      options: {
        outputStyle: 'extended',
        sourceMap: true
      },
      files: {
        'assets/dist/app.css': 'resources/assets/sass/app.scss'
      }
    },
    build: {
      options: {
        outputStyle: 'compressed',
        sourceMap: false
      },
      files: {
        'assets/dist/app.css': 'resources/assets/sass/app.scss'
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
        'node_modules/bootstrap-sass/assets/javascripts/bootstrap.js'
      ],
      dest: 'assets/dist/dependencies.js'
    }
  });

  grunt.config('uglify', {
    js_dependencies: {
      files: {
        'assets/dist/dependencies.js': ['assets/dist/dependencies.js']
      }
    }
  });

  grunt.config('copy', {
    fontawesome: {
      expand: true,
      flatten: true,
      src: ['node_modules/font-awesome/fonts/*'],
      dest: 'assets/dist/fonts/'
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
    'sass:dev',
    'postcss:dev',
    'clean:js',
    'concat:js_dependencies',
    'copy:fontawesome'
  ]);

  grunt.registerTask('dev', [
    'clean:styles',
    'sass:dev',
    'postcss:dev',
    'clean:js',
    'concat:js_dependencies',
    'concat:js_dependencies',
    'copy:fontawesome',
    'watch'
  ]);

  grunt.registerTask('build', [
    'clean:styles',
    'sass:build',
    'postcss:build',
    'clean:js',
    'concat:js_dependencies',
    'uglify:js_dependencies',
    'copy:fontawesome'
  ]);
};
