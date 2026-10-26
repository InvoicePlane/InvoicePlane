"use strict";

/*
 * Runs on the built-in Node test runner, no dependencies:
 *   node --test tests/js/
 *
 * Covers the jQuery ajaxPrefilter in resources/assets/core/js/scripts.js that appends the CSRF token to
 * every POST. The script is a plain browser script, so it is evaluated here with a stub document and a
 * stub jQuery that hands back the registered prefilter.
 */

const test = require("node:test");
const assert = require("node:assert/strict");
const fs = require("node:fs");
const path = require("node:path");
const vm = require("node:vm");

const SOURCE = fs.readFileSync(path.join(__dirname, "../../resources/assets/core/js/scripts.js"), "utf8");

function loadPrefilter(tokenValue = "tok123") {
    const absorb = () => new Proxy(function () {}, {
        get: (_t, prop) => (prop === Symbol.toPrimitive ? () => "" : absorb()),
        apply: () => absorb(),
    });
    const meta = { getAttribute: () => tokenValue, setAttribute() {} };
    let prefilter = null;

    const $ = new Proxy(function () {}, {
        get: (_t, prop) => (prop === "ajaxPrefilter" ? (fn) => { prefilter = fn; } : absorb()),
        apply: (_t, _this, args) => {
            if (typeof args[0] === "function") {
                args[0]();
            }
            return absorb();
        },
    });

    const known = {
        querySelector: (selector) => (selector.includes("csrf_token_name")
            ? { getAttribute: () => "_ip_csrf" }
            : meta),
    };
    const document = new Proxy(known, { get: (target, prop) => (prop in target ? target[prop] : absorb()) });

    const sandbox = { $, jQuery: $, ClipboardJS: function () { return absorb(); }, FormData, document, window: { location: { href: "http://test/" } }, console, setTimeout, clearTimeout };
    sandbox.window.document = document;
    vm.createContext(sandbox);
    vm.runInContext(SOURCE, sandbox);

    assert.equal(typeof prefilter, "function", "scripts.js must register an ajaxPrefilter");

    return prefilter;
}

test("prefilter: appends the token to an existing urlencoded body", () => {
    const options = { type: "POST", data: "filter_query=abc" };

    loadPrefilter()(options);

    assert.equal(options.data, "filter_query=abc&_ip_csrf=tok123");
});

test("prefilter: an empty body becomes the bare token pair, never a query-string fragment", () => {
    const options = { type: "POST", data: "" };

    loadPrefilter()(options);

    assert.equal(options.data, "_ip_csrf=tok123", "a leading '?' turns the field name into '?_ip_csrf' and the server answers 403");
});

test("prefilter: a POST without any data still carries the token and no literal 'undefined'", () => {
    const options = { type: "POST" };

    loadPrefilter()(options);

    assert.equal(options.data, "_ip_csrf=tok123");
});

test("prefilter: a FormData body keeps its fields and gains the token as a field", () => {
    const form = new FormData();
    form.append("file_name", "logo.png");
    const options = { type: "POST", data: form, processData: false, contentType: false };

    loadPrefilter()(options);

    assert.ok(options.data instanceof FormData, "FormData must not be turned into the string '[object FormData]'");
    assert.equal(options.data.get("file_name"), "logo.png");
    assert.equal(options.data.get("_ip_csrf"), "tok123");
});

test("prefilter: GET requests are left alone", () => {
    const options = { type: "GET", data: "q=1" };

    loadPrefilter()(options);

    assert.equal(options.data, "q=1");
});
