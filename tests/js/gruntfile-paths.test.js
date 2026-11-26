"use strict";

// The Gruntfile is the contract between the asset sources (resources/assets) and the web root
// (public/assets). This loads it with the real grunt and checks the generated mapping, without
// running a build.

const test = require("node:test");
const assert = require("node:assert/strict");
const fs = require("node:fs");
const Module = require("node:module");
const path = require("node:path");

const root = path.resolve(__dirname, "../..");

// The JS workflow runs on the bare Node test runner without "yarn install", so the Gruntfile is
// loaded against a minimal grunt double: initConfig() keeps the object, config.get() reads a dotted
// path, file.expand() lists real files below cwd, and the packages the Gruntfile requires are stubbed.
function listFiles(dir, prefix = "") {
  return fs.readdirSync(dir, { withFileTypes: true }).flatMap((entry) =>
    entry.isDirectory() ? listFiles(path.join(dir, entry.name), prefix + entry.name + "/") : [prefix + entry.name]
  );
}

function loadConfig() {
  let config;
  const grunt = {
    initConfig(c) {
      config = c;
    },
    registerTask() {},
    file: {
      expand({ cwd }, pattern) {
        // only the "**/sass/*.scss" pattern is used with expand()
        assert.equal(pattern, "**/sass/*.scss");
        return listFiles(path.join(root, cwd)).filter((f) => /(^|\/)sass\/[^/]+\.scss$/.test(f));
      },
    },
  };
  const realLoad = Module._load;
  Module._load = function (request, ...rest) {
    if (["sass", "load-grunt-tasks", "autoprefixer"].includes(request)) return request === "load-grunt-tasks" ? () => {} : {};
    return realLoad.call(this, request, ...rest);
  };
  try {
    delete require.cache[require.resolve(path.join(root, "Gruntfile.js"))];
    require(path.join(root, "Gruntfile.js"))(grunt);
  } finally {
    Module._load = realLoad;
  }
  return {
    get: (dotted) => dotted.split(".").reduce((node, key) => node[key], config),
  };
}

test("every theme stylesheet is built from resources/assets into public/assets", () => {
  const files = loadConfig().get("sass.build.files");

  const entries = Object.entries(files);
  assert.ok(entries.length >= 8, "both themes and the welcome/report/template sheets are mapped");
  for (const [dest, src] of entries) {
    assert.match(src, /^resources\/assets\/[a-z_]+\/sass\/[a-z_-]+\.scss$/);
    assert.match(dest, /^public\/assets\/[a-z_]+\/css\/[a-z_-]+\.css$/);
    assert.equal(
      dest.replace(/^public\/assets\//, "").replace("/css/", "/sass/").replace(/\.css$/, ""),
      src.replace(/^resources\/assets\//, "").replace(/\.scss$/, "")
    );
  }
  assert.ok(files["public/assets/invoiceplane/css/style.css"]);
});

test("javascript bundles are written to public/assets and never back into the sources", () => {
  const config = loadConfig();

  const uglify = config.get("uglify.build.files");
  for (const dest of Object.keys(uglify)) assert.match(dest, /^public\/assets\/core\/js\/[a-z]+\.min\.js$/);
  assert.equal(uglify["public/assets/core/js/scripts.min.js"][0], "resources/assets/core/js/scripts.js");
  assert.equal(config.get("concat.dependencies.dest"), "public/assets/core/js/dependencies.js");
  assert.ok(config.get("concat.dependencies.src").includes("resources/assets/core/js/jquery-ui.js"));
});

test("clean only ever touches public/assets and keeps its .gitignore", () => {
  const patterns = loadConfig().get("clean.basic");

  assert.ok(patterns.includes("public/assets/**"));
  assert.ok(patterns.includes("!public/assets/.gitignore"));
  for (const p of patterns) assert.ok(p.replace(/^!/, "").startsWith("public/assets"), `unexpected clean target ${p}`);
});

test("static files are copied from the sources next to the build output", () => {
  const [entry] = loadConfig().get("copy.static.files");

  assert.equal(entry.cwd, "resources/assets");
  assert.equal(entry.dest, "public/assets/");
  assert.ok(entry.src.includes("*/*.theme"));
  assert.ok(entry.src.includes("core/img/**"));
});
