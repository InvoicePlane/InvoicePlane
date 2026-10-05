"use strict";
module.exports = function(grunt) {
  const sass = require("sass");

  // Load grunt tasks automatically
  require("load-grunt-tasks")(grunt);

  // MODULES

  grunt.initConfig({
    clean: {
      basic: [
        "public/assets/**/*.css",
        "public/assets/**/*.css.map",
        "!public/assets/core/css/custom.css",
        "!public/assets/core/css/custom-pdf.css", // CSS
        "!public/assets/core/css/paypal.css", // CSS
        "public/assets/core/js/*.js",
        "!public/assets/core/js/scripts.js",
        "!public/assets/core/js/jquery-ui.js", // JS
        "!public/assets/core/js/paypal.js", // JS
        "!public/assets/core/js/integration-settings.js", // JS
        "public/assets/core/fonts/*",
        "!public/assets/core/fonts/.gitignore" // Fonts
      ],
      build: ["public/assets/default/js/dependencies.js", "public/assets/default/js/legacy.js"]
    },

    sass: {
      dev: {
        options: {
          implementation: sass,
          outputStyle: "expanded",
          sourceMap: true
        },
        files: grunt.file.expandMapping(["public/assets/**/sass/*.scss"], "css", {
          rename: function(dest, matched) {
            return matched.replace(/\/sass\//, "/" + dest + "/").replace(/\.scss$/, ".css");
          }
        })
      },
      build: {
        options: {
          implementation: sass,
          outputStyle: "compressed"
        },
        files: grunt.file.expandMapping(["public/assets/**/sass/*.scss"], "css", {
          rename: function(dest, matched) {
            return matched.replace(/\/sass\//, "/" + dest + "/").replace(/\.scss$/, ".css");
          }
        })
      }
    },

    postcss: {
      dev: {
        options: {
          map: true,
          processors: [require("autoprefixer")]
        },
        src: ["public/assets/**/css/*.css", "!public/assets/core/css/custom.css", "!public/assets/core/css/custom-pdf.css"]
      },
      build: {
        options: {
          map: false,
          processors: [require("autoprefixer")]
        },
        src: ["public/assets/**/css/*.css", "!public/assets/core/css/custom.css", "!public/assets/core/css/custom-pdf.css"]
      }
    },

    concat: {
      legacy: {
        src: ["node_modules/html5shiv/dist/html5shiv.js"],
        dest: "public/assets/core/js/legacy.js"
      },
      dependencies: {
        src: [
          "node_modules/jquery/dist/jquery.js",
          "node_modules/js-cookie/src/js.cookie.js",
          "public/assets/core/js/jquery-ui.js",
          "node_modules/bootstrap-sass/assets/javascripts/bootstrap.js",
          "node_modules/bootstrap-datepicker/js/bootstrap-datepicker.js",
          "node_modules/select2/dist/js/select2.full.js",
          "node_modules/dropzone/dist/dropzone.js",
          "node_modules/clipboard/dist/clipboard.js"
        ],
        dest: "public/assets/core/js/dependencies.js"
      },
      zxcvbn: {
        src: ["node_modules/zxcvbn/dist/zxcvbn.js"],
        dest: "public/assets/core/js/zxcvbn.js"
      }
    },

    uglify: {
      build: {
        files: {
          "public/assets/core/js/legacy.min.js": ["public/assets/core/js/legacy.js"],
          "public/assets/core/js/dependencies.min.js": ["public/assets/core/js/dependencies.js"],
          "public/assets/core/js/scripts.min.js": ["public/assets/core/js/scripts.js"]
        }
      }
    },

    copy: {
      datepickerlocale: {
        expand: true,
        flatten: true,
        src: ["node_modules/bootstrap-datepicker/js/locales/**"],
        dest: "public/assets/core/js/locales/",
        filter: "isFile"
      },
      select2locale: {
        expand: true,
        flatten: true,
        src: ["node_modules/select2/dist/js/i18n/**"],
        dest: "public/assets/core/js/locales/select2/",
        filter: "isFile"
      },
      fontawesome: {
        expand: true,
        flatten: true,
        src: ["node_modules/font-awesome/fonts/*"],
        dest: "public/assets/core/fonts"
      },
      devjs: {
        files: [
          {
            cwd: "public/assets/core/js/",
            src: ["*.js", "!jquery-ui.js"],
            dest: "public/assets/core/js/",
            expand: true,
            rename: function(dest, src) {
              return (dest + src).replace(".js", ".min.js");
            }
          }
        ]
      }
    },

    watch: {
      sass: {
        files: "public/assets/**/*.scss",
        tasks: ["sass:dev", "postcss:dev"]
      },
      js: {
        files: "public/assets/core/js/scripts.js",
        tasks: ["uglify"]
      }
    }
  });

  // TASKS

  grunt.registerTask("default", "build");

  grunt.registerTask("dev-build", [
    "clean:basic",
    "sass:dev",
    "postcss:dev",
    "concat:legacy",
    "concat:dependencies",
    "concat:zxcvbn",
    "copy:datepickerlocale",
    "copy:select2locale",
    "copy:fontawesome",
    "copy:devjs"
  ]);

  grunt.registerTask("dev", [
    "clean:basic",
    "sass:dev",
    "postcss:dev",
    "concat:legacy",
    "concat:dependencies",
    "concat:zxcvbn",
    "copy:datepickerlocale",
    "copy:select2locale",
    "copy:fontawesome",
    "copy:devjs",
    "watch"
  ]);

  grunt.registerTask("build", [
    "clean:basic",
    "sass:build",
    "postcss:build",
    "concat:legacy",
    "concat:dependencies",
    "concat:zxcvbn",
    "uglify:build",
    "clean:build",
    "copy:datepickerlocale",
    "copy:select2locale",
    "copy:fontawesome"
  ]);
};
