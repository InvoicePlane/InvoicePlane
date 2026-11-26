"use strict";
module.exports = function(grunt) {
  const sass = require("sass");

  // Sources live in resources/assets (scss, hand-written js and css, images, theme descriptors).
  // Everything the browser loads is generated into public/assets, the web root.
  const SRC = "resources/assets";
  const DEST = "public/assets";

  // resources/assets/<theme>/sass/<name>.scss -> public/assets/<theme>/css/<name>.css
  const sassFiles = function() {
    const files = {};
    grunt.file.expand({ cwd: SRC }, "**/sass/*.scss").forEach(function(file) {
      files[DEST + "/" + file.replace(/\/sass\//, "/css/").replace(/\.scss$/, ".css")] = SRC + "/" + file;
    });
    return files;
  };

  // Load grunt tasks automatically
  require("load-grunt-tasks")(grunt);

  // MODULES

  grunt.initConfig({
    clean: {
      basic: [DEST + "/**", "!" + DEST, "!" + DEST + "/.gitignore"],
      build: [DEST + "/core/js/dependencies.js", DEST + "/core/js/legacy.js"]
    },

    sass: {
      dev: {
        options: {
          implementation: sass,
          outputStyle: "expanded",
          sourceMap: true
        },
        files: sassFiles()
      },
      build: {
        options: {
          implementation: sass,
          outputStyle: "compressed"
        },
        files: sassFiles()
      }
    },

    postcss: {
      dev: {
        options: {
          map: true,
          processors: [require("autoprefixer")]
        },
        src: [DEST + "/**/css/*.css", "!" + DEST + "/core/css/custom.css", "!" + DEST + "/core/css/custom-pdf.css", "!" + DEST + "/core/css/paypal.css"]
      },
      build: {
        options: {
          map: false,
          processors: [require("autoprefixer")]
        },
        src: [DEST + "/**/css/*.css", "!" + DEST + "/core/css/custom.css", "!" + DEST + "/core/css/custom-pdf.css", "!" + DEST + "/core/css/paypal.css"]
      }
    },

    concat: {
      legacy: {
        src: ["node_modules/html5shiv/dist/html5shiv.js"],
        dest: DEST + "/core/js/legacy.js"
      },
      dependencies: {
        src: [
          "node_modules/jquery/dist/jquery.js",
          "node_modules/js-cookie/src/js.cookie.js",
          SRC + "/core/js/jquery-ui.js",
          "node_modules/bootstrap-sass/assets/javascripts/bootstrap.js",
          "node_modules/bootstrap-datepicker/js/bootstrap-datepicker.js",
          "node_modules/select2/dist/js/select2.full.js",
          "node_modules/dropzone/dist/dropzone.js",
          "node_modules/clipboard/dist/clipboard.js"
        ],
        dest: DEST + "/core/js/dependencies.js"
      },
      zxcvbn: {
        src: ["node_modules/zxcvbn/dist/zxcvbn.js"],
        dest: DEST + "/core/js/zxcvbn.js"
      }
    },

    uglify: {
      build: {
        files: {
          [DEST + "/core/js/legacy.min.js"]: [DEST + "/core/js/legacy.js"],
          [DEST + "/core/js/dependencies.min.js"]: [DEST + "/core/js/dependencies.js"],
          [DEST + "/core/js/scripts.min.js"]: [SRC + "/core/js/scripts.js"]
        }
      }
    },

    copy: {
      datepickerlocale: {
        expand: true,
        flatten: true,
        src: ["node_modules/bootstrap-datepicker/js/locales/**"],
        dest: DEST + "/core/js/locales/",
        filter: "isFile"
      },
      select2locale: {
        expand: true,
        flatten: true,
        src: ["node_modules/select2/dist/js/i18n/**"],
        dest: DEST + "/core/js/locales/select2/",
        filter: "isFile"
      },
      fontawesome: {
        expand: true,
        flatten: true,
        src: ["node_modules/font-awesome/fonts/*"],
        dest: DEST + "/core/fonts"
      },
      static: {
        files: [
          // images, hand-written css and js, and the theme descriptors are served as they are
          { expand: true, cwd: SRC, src: ["core/img/**", "core/css/*.css", "core/js/*.js", "*/*.theme"], dest: DEST + "/" }
        ]
      },
      devjs: {
        files: [
          {
            cwd: DEST + "/core/js/",
            src: ["*.js", "!jquery-ui.js", "!*.min.js"],
            dest: DEST + "/core/js/",
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
        files: SRC + "/**/*.scss",
        tasks: ["sass:dev", "postcss:dev"]
      },
      js: {
        files: SRC + "/core/js/scripts.js",
        tasks: ["uglify"]
      }
    }
  });

  // TASKS

  grunt.registerTask("default", "build");

  grunt.registerTask("dev-build", [
    "clean:basic",
    "copy:static",
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
    "copy:static",
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
    "copy:static",
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
