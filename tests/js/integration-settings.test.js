"use strict";

/*
 * Runs on the built-in Node test runner, no dependencies:
 *   node --test tests/js/
 *
 * Covers assets/core/js/integration-settings.js — the behaviour behind the
 * ".js-test-connection" button on the e-invoice provider settings form: a
 * jQuery POST to Settings::test_connection() (which calls the provider client's
 * ping()) and the rendering of the {reachable, http_code, message} outcome.
 */

const test = require("node:test");
const assert = require("node:assert/strict");

const { describePing, readMessages, bindTestConnection } = require("../../assets/core/js/integration-settings.js");

const MESSAGES = {
    running: "Testing the connection…",
    ok: "Connection successful.",
    failed: "Connection failed.",
};

// ---------------------------------------------------------------------------
// describePing — pure envelope -> view mapping
// ---------------------------------------------------------------------------

test("describePing: a reachable envelope renders the success class and message with the HTTP code", () => {
    const view = describePing({ reachable: true, http_code: 200, message: "2 checkout sessions listed" }, MESSAGES);

    assert.equal(view.reachable, true);
    assert.match(view.cssClass, /\btext-success\b/);
    assert.equal(view.text, "Connection successful. — 2 checkout sessions listed (HTTP 200)");
});

test("describePing: an unreachable envelope renders the danger class and failure message", () => {
    const view = describePing({ reachable: false, http_code: 401, message: "Invalid API Key" }, MESSAGES);

    assert.equal(view.reachable, false);
    assert.match(view.cssClass, /\btext-danger\b/);
    assert.equal(view.text, "Connection failed. — Invalid API Key (HTTP 401)");
});

test("describePing: accepts a JSON string body (jQuery may hand back text)", () => {
    const view = describePing('{"reachable":true,"http_code":200,"message":""}', MESSAGES);

    assert.equal(view.reachable, true);
    assert.equal(view.text, "Connection successful. (HTTP 200)");
});

test("describePing: an unparseable body is treated as unreachable, never throws", () => {
    const view = describePing("<html>gateway timeout</html>", MESSAGES);

    assert.equal(view.reachable, false);
    assert.equal(view.text, "Connection failed.");
});

test("describePing: only a strict boolean true counts as reachable", () => {
    assert.equal(describePing({}, MESSAGES).reachable, false);
    assert.equal(describePing({ reachable: "true" }, MESSAGES).reachable, false);
    assert.equal(describePing({ reachable: 1 }, MESSAGES).reachable, false);
    assert.equal(describePing(null, MESSAGES).reachable, false);
});

// ---------------------------------------------------------------------------
// readMessages — the button's data-msg-* attributes
// ---------------------------------------------------------------------------

test("readMessages: reads the three UI strings off the button, with fallbacks", () => {
    const btn = fakeButton({ "data-msg-running": "R", "data-msg-ok": "O", "data-msg-failed": "F" });
    assert.deepEqual(readMessages(btn), { running: "R", ok: "O", failed: "F" });

    const bare = fakeButton({});
    const m = readMessages(bare);
    assert.equal(typeof m.running, "string");
    assert.equal(typeof m.ok, "string");
    assert.equal(typeof m.failed, "string");
});

// ---------------------------------------------------------------------------
// bindTestConnection — click -> jQuery POST -> render
// ---------------------------------------------------------------------------

test("bindTestConnection: a click POSTs to the button's data-url and shows the running state", () => {
    const btn = fakeButton({
        "data-url": "/integrations/settings/test_connection/7",
        "data-msg-running": MESSAGES.running,
        "data-msg-ok": MESSAGES.ok,
        "data-msg-failed": MESSAGES.failed,
    });
    const out = fakeResult();
    const request = fakeDeferred();
    let postedUrl = null;
    const $ = { post: (url) => { postedUrl = url; return request; } };

    const bound = bindTestConnection($, fakeScope(btn, out));
    assert.equal(bound, btn);

    btn.click();

    assert.equal(postedUrl, "/integrations/settings/test_connection/7");
    assert.equal(btn.disabled, true);
    assert.equal(out.textContent, MESSAGES.running);
});

test("bindTestConnection: a reachable response renders success and re-enables the button", () => {
    const btn = fakeButton({
        "data-url": "/x",
        "data-msg-running": MESSAGES.running,
        "data-msg-ok": MESSAGES.ok,
        "data-msg-failed": MESSAGES.failed,
    });
    const out = fakeResult();
    const request = fakeDeferred();
    bindTestConnection({ post: () => request }, fakeScope(btn, out)).click();

    request.resolveDone({ reachable: true, http_code: 200, message: "" });

    assert.match(out.className, /\btext-success\b/);
    assert.equal(out.textContent, "Connection successful. (HTTP 200)");
    assert.equal(btn.disabled, false);
});

test("bindTestConnection: a failed request renders the failure message and re-enables the button", () => {
    const btn = fakeButton({ "data-url": "/x", "data-msg-failed": MESSAGES.failed });
    const out = fakeResult();
    const request = fakeDeferred();
    bindTestConnection({ post: () => request }, fakeScope(btn, out)).click();

    request.resolveFail();

    assert.match(out.className, /\btext-danger\b/);
    assert.equal(out.textContent, MESSAGES.failed);
    assert.equal(btn.disabled, false);
});

test("bindTestConnection: nothing to bind (no button in scope) returns null and does not throw", () => {
    assert.equal(bindTestConnection({ post() {} }, { querySelector: () => null }), null);
});

test("bindTestConnection: a missing jQuery returns null and does not throw", () => {
    const btn = fakeButton({ "data-url": "/x" });
    assert.equal(bindTestConnection(null, fakeScope(btn, fakeResult())), null);
});

// ---------------------------------------------------------------------------
// Test doubles
// ---------------------------------------------------------------------------

function fakeButton(attrs) {
    const listeners = {};
    return {
        disabled: false,
        getAttribute: (name) => (Object.prototype.hasOwnProperty.call(attrs, name) ? attrs[name] : null),
        addEventListener: (event, fn) => { listeners[event] = fn; },
        click: () => { if (listeners.click) { listeners.click(); } },
    };
}

function fakeResult() {
    return { className: "", textContent: "" };
}

function fakeScope(btn, out) {
    return {
        querySelector: (selector) => {
            if (selector === ".js-test-connection") { return btn; }
            if (selector === ".js-test-connection-result") { return out; }
            return null;
        },
    };
}

// Minimal jQuery.Deferred-like object: chainable done/fail/always.
function fakeDeferred() {
    const handlers = { done: [], fail: [], always: [] };
    const promise = {
        done(fn) { handlers.done.push(fn); return promise; },
        fail(fn) { handlers.fail.push(fn); return promise; },
        always(fn) { handlers.always.push(fn); return promise; },
        resolveDone(data) {
            handlers.done.forEach((fn) => fn(data));
            handlers.always.forEach((fn) => fn());
        },
        resolveFail() {
            handlers.fail.forEach((fn) => fn());
            handlers.always.forEach((fn) => fn());
        },
    };
    return promise;
}
