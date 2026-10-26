"use strict";

/*
 * Runs on the built-in Node test runner, no dependencies:
 *   node --test tests/js/
 *
 * Covers the inline script of application/modules/settings/views/partial_settings_updates.php: the update
 * check and the news panel on the settings page. They ask GitHub first and fall back to the two legacy IDS
 * hosts (ids.invoiceplane.com, then ids.invoiceplane.org), and end in a "failed" state when nothing answers.
 *
 * The script is inline PHP/JS, so it is read from the view, its few PHP echoes are replaced by fixed values
 * and it is run against a fake jQuery that records every $.ajax() call and lets the test answer it.
 */

const test = require("node:test");
const assert = require("node:assert/strict");
const fs = require("node:fs");
const path = require("node:path");

const VIEW = path.join(__dirname, "../../application/modules/settings/views/partial_settings_updates.php");

function inlineScript() {
    const php = fs.readFileSync(VIEW, "utf8");

    return php
        .match(/<script[^>]*>([\s\S]*?)<\/script>/g)
        .map((block) => block.replace(/<\/?script[^>]*>/g, ""))
        .join("\n")
        .replace(/<\?php echo json_encode\(trans\('date'\)\); \?>/g, '"Date"')
        .replace(/<\?php echo trans\('date'\)\s*\?>/g, "Date")
        .replace(/<\?php echo get_setting\('current_version'\); \?>/g, "1.0.0")
        .replace(/<\?php echo IP_DEBUG \? 'console\.log\(data\);' : ''; \?>/g, "")
        .replace(/<\?php[\s\S]*?\?>/g, "");
}

/**
 * Runs the page script once. `answer(options)` is called for every $.ajax() the script makes and may call
 * options.success(...) or options.error(...); it can issue further requests, which are answered the same way.
 */
function run(answer) {
    const calls = [];
    const shown = [];
    const element = new Proxy(function () {}, {
        get: (target, key) => {
            if (key === "then") return undefined;
            if (key === Symbol.toPrimitive || key === "toString") return () => "element";
            if (key === "length") return 1;
            return (...args) => {
                shown.push(String(key) + ":" + args.join(","));
                return element;
            };
        },
        apply: () => element,
    });
    const $ = (argument) => {
        if (typeof argument === "function") argument();
        return element;
    };
    $.ajax = (options) => {
        calls.push(options);
        answer(options);
    };
    $.fn = {};

    const context = {
        $,
        jQuery: $,
        setTimeout: (callback) => callback(),
        document: { createTextNode: () => element, readyState: "complete" },
        console,
        window: {},
    };
    let error = null;
    try {
        new Function(...Object.keys(context), inlineScript())(...Object.values(context));
    } catch (caught) {
        error = caught;
    }

    return { calls, shown, error };
}

const isGithubNews = (options) => options.url.includes("api.github.com") && options.url.includes("releases?");
const isGithubLatest = (options) => options.url.includes("api.github.com") && !options.url.includes("releases?");

test("every request fails: all six release and IDS requests carry the same bounded timeout", () => {
    const { calls, error } = run((options) => options.error && options.error({}));

    assert.equal(error, null);
    assert.equal(calls.length, 6, "update check (GitHub, .com, .org) and news (GitHub, .com, .org)");
    const timeouts = new Set(calls.map((call) => call.timeout));
    assert.equal(timeouts.size, 1, "one shared timeout value, saw " + [...timeouts].join(","));
    const [timeout] = [...timeouts];
    assert.ok(Number.isInteger(timeout) && timeout > 0 && timeout <= 30000, "bounded, saw " + timeout);
});

test("every request fails: the update check and the news both end in their failed state", () => {
    const { shown, error } = run((options) => options.error && options.error({}));

    assert.equal(error, null);
    assert.ok(shown.some((entry) => entry.startsWith("removeClass:hidden")), "a failed state is revealed");
});

test("GitHub news that is not an array falls back to the first IDS host", () => {
    const { calls, error } = run((options) => {
        if (isGithubNews(options)) options.success({ message: "API rate limit exceeded" });
        else if (isGithubLatest(options)) options.error({});
    });

    assert.equal(error, null);
    assert.ok(calls.some((call) => call.url.includes("ids.invoiceplane.com/get_news")));
});

test("the first IDS news host answering with something that is not a list moves on to the second host", () => {
    const { calls, error } = run((options) => {
        if (options.url.includes("api.github.com")) options.error({});
        else if (options.url.includes("ids.invoiceplane.com/get_news")) options.success({ unexpected: true });
    });

    assert.equal(error, null);
    assert.ok(calls.some((call) => call.url.includes("ids.invoiceplane.org/get_news")), "the .org host was not asked");
});

test("the second IDS news host answering with something that is not a list shows the failed state", () => {
    const { shown, error } = run((options) => {
        if (options.url.includes("api.github.com")) options.error({});
        else if (options.url.includes("ids.invoiceplane.com/get_news")) options.error({});
        else if (options.url.includes("ids.invoiceplane.org/get_news")) options.success({ unexpected: true });
    });

    assert.equal(error, null);
    assert.ok(shown.some((entry) => entry.startsWith("removeClass:hidden")), "the failed state was not revealed");
});

test("a valid GitHub release list is rendered", () => {
    const { shown, error } = run((options) => {
        if (isGithubNews(options)) options.success([{ name: "1.8.0", tag_name: "v1.8.0", published_at: "2026-01-01T00:00:00Z" }]);
        else options.error({});
    });

    assert.equal(error, null);
    assert.ok(shown.some((entry) => entry.startsWith("append:")), "nothing was appended for a valid release list");
});
