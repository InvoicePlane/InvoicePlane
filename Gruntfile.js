module.exports = grunt => {

  // load grunt tasks automatically
  require('load-grunt-tasks')(grunt);

  // track the time each task takes
  require('time-grunt')(grunt);

  // default config
  grunt.initConfig({
    paths: {
      assets: './resources/assets',
      dist: './assets/dist'
    }
  });

  // Scripts
  grunt.config('browserify', {
    prod: {
      options: {
        transform: ['babelify', ['uglifyify', {global: true}]],
        browserifyOptions: {
          debug: true
        }
      },
      files: [
        {
          src: '<%= paths.assets %>/js/app.js',
          dest: '<%= paths.dist %>/app.js'
        }
      ]
    }
  });

  // Browsersync
  grunt.config('browserSync', {
    prod: {
      bsFiles: {
        src: [
          '<%= paths.dist %>/*.js',
          '<%= paths.dist %>/*.css'
        ]
      },
      options: {
        port: 3010,
        watchTask: true,
        notify: false,
        open: false,
        ghostMode: false
      }
    }
  });

  // Concat
  grunt.config('concat', {
    js_dependencies: {
      src: [
        'node_modules/jquery/dist/jquery.min.js',
        'node_modules/jquery-ui-dist/jquery-ui.min.js',
        'node_modules/bootstrap/dist/js/bootstrap.bundle.min.js',
        'node_modules/autosize/dist/autosize.min.js',
        'node_modules/moment/min/moment.min.js',
        'node_modules/bootstrap-notify/bootstrap-notify.min.js',
        'node_modules/jquery-slimscroll/jquery.slimscroll.min.js'
      ],
      dest: '<%= paths.dist %>/dependencies.js'
    }
  });

  // Post CSS
  const autoprefixer = require('autoprefixer');

  grunt.config('postcss', {
    prod: {
      options: {
        map: true,
        processors: [
          autoprefixer({
            browsers: 'last 3 version'
          })
        ]
      },
      src: '<%= paths.dist %>/app.css'
    }
  });

  // CSS
  const sass = require('node-sass');

  grunt.config('sass', {
    options: {
      implementation: sass
    },
    build: {
      options: {
        outputStyle: 'compressed',
        sourceMap: true
      },
      files: {
        '<%= paths.dist %>/app.css': '<%= paths.assets %>/sass/app.scss'
      }
    }
  });

  // Copy tasks
  grunt.config('copy', {
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

  // File watcher
  grunt.config('watch', {
    sass: {
      files: 'resources/assets/sass/**/*.scss',
      tasks: ['sass', 'postcss']
    },
    js: {
      files: 'resources/assets/js/**/*.js',
      tasks: ['browserify']
    }
  });

  // Tasks
  grunt.registerTask('build', [
    'browserify',
    'concat',
    'sass',
    'postcss',
    'copy'
  ]);

  grunt.registerTask('dev', [
    'build',
    'watch',
    'browserSync'
  ]);

  grunt.registerTask('default', ['build']);

};
