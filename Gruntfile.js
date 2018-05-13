"use strict";
module.exports = function (grunt) {

  // Load grunt tasks automatically
  require("load-grunt-tasks")(grunt);

  // MODULES
  grunt.config("clean", {
    basic: [
      "public/assets/core/*.css", "public/assets/core/*.css.map", // CSS
      "public/assets/core/*.js", // JS
      "public/assets/fonts/*", "!public/assets/fonts/.gitignore" // Fonts
    ]
  });

  grunt.config("sass", {
    dev: {
      options: {
        outputStyle: "extended",
        sourceMap: true
      },
      files: {
        "public/assets/core/core.css": "resources/assets/scss/core.scss"
      }
    },
    build: {
      options: {
        outputStyle: "compressed"
      },
      files: {
        "public/assets/core/core.css": "resources/assets/scss/core.scss"
      }
    }
  });

  grunt.config("postcss", {
    dev: {
      options: {
        map: true,
        processors: [
          require("autoprefixer")({
            browsers: "last 3 version"
          })
        ]
      },
      src: [
        "public/assets/core/*.css"
      ]
    },
    build: {
      options: {
        map: false,
        processors: [
          require("autoprefixer")({
            browsers: "last 3 version"
          })
        ]
      },
      src: [
        "public/assets/core/*.css"
      ]
    }
  });

  grunt.config("concat", {
    dependencies: {
      src: [
        "node_modules/jquery/dist/jquery.js",
        "node_modules/popper.js/dist/umd/popper.js",
        "node_modules/bootstrap/dist/js/bootstrap.js"
      ],
      dest: "public/assets/core/dependencies.js"
    }
  });

  grunt.config("uglify", {
    build: {
      files: {
        "public/assets/core/dependencies.min.js": ["public/assets/core/dependencies.js"],
        "public/assets/core/core.min.js": ["public/assets/core/core.js"]
      }
    }
  });

  grunt.config("copy", {
    fontawesome: {
      expand: true,
      flatten: true,
      src: ["node_modules/font-awesome/fonts/*"],
      dest: "assets/core/fonts"
    }
  });

  grunt.config("watch", {
    sass: {
      files: "resources/assets/scss/*.scss",
      tasks: ["sass:dev", "postcss:dev"]
    },
    js: {
      files: "resources/assets/js/core.js",
      tasks: ["uglify"]
    }
  });

  // TASKS

  grunt.registerTask("default", "build");

  grunt.registerTask("dev-build", [
    "clean:basic",
    "sass:dev",
    "postcss:dev",
    "concat:dependencies"
  ]);

  grunt.registerTask("dev", [
    "clean:basic",
    "sass:dev",
    "postcss:dev",
    "concat:dependencies",
    "watch"
  ]);

  grunt.registerTask("build", [
    "clean:basic",
    "sass:build",
    "postcss:build",
    "concat:dependencies"
  ]);
};
