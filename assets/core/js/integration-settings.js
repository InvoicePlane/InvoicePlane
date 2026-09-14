/*
 * InvoicePlane — e-invoice provider settings
 *
 * Behaviour for the "Test connection" button on the provider settings form
 * (application/modules/integrations/views/provider_form.php). Clicking it POSTs
 * to Settings::test_connection(), which resolves the provider client and calls
 * its ping(); this renders the {reachable, http_code, message} outcome on the
 * result line next to the button.
 *
 * Loaded raw from the view (like paypal.js) and covered by
 * tests/js/integration-settings.test.js on the Node test runner.
 */
(function (root, factory) {
    "use strict";

    var api = factory();

    if (typeof module === "object" && module.exports) {
        module.exports = api; // Node test runner
    } else {
        root.IntegrationSettings = api;

        if (root.jQuery && typeof root.document !== "undefined") {
            root.jQuery(function () {
                api.bindTestConnection(root.jQuery, root.document);
            });
        }
    }
})(typeof window !== "undefined" ? window : this, function () {
    "use strict";

    /**
     * Map a ping() envelope — an object, or the JSON string jQuery may hand
     * back — to what the result line should show.
     *
     * @param {Object|string|null} data      {reachable, http_code, message}
     * @param {{running:string, ok:string, failed:string}} messages
     * @returns {{reachable:boolean, cssClass:string, text:string}}
     */
    function describePing(data, messages) {
        if (typeof data === "string") {
            try {
                data = JSON.parse(data);
            } catch (e) {
                data = {};
            }
        }

        data = data || {};

        var reachable = data.reachable === true;
        var detail = (data.message ? " — " + data.message : "") +
            (data.http_code ? " (HTTP " + data.http_code + ")" : "");

        return {
            reachable: reachable,
            cssClass: "js-test-connection-result " + (reachable ? "text-success" : "text-danger"),
            text: (reachable ? messages.ok : messages.failed) + detail
        };
    }

    /**
     * Read the three UI strings off the button's data-msg-* attributes, with
     * English fallbacks so the module is usable without a rendered view.
     */
    function readMessages(btn) {
        return {
            running: btn.getAttribute("data-msg-running") || "Testing the connection…",
            ok: btn.getAttribute("data-msg-ok") || "Connection successful.",
            failed: btn.getAttribute("data-msg-failed") || "Connection failed."
        };
    }

    /**
     * Wire the button: on click, POST to its data-url and render the outcome.
     *
     * @param {{post:function}} $      jQuery (or a stand-in exposing .post())
     * @param {Document|Element} scope element to query the button/result from
     * @returns {Element|null} the button, or null when there is nothing to bind
     */
    function bindTestConnection($, scope) {
        scope = scope || (typeof document !== "undefined" ? document : null);

        if (!$ || !scope) {
            return null;
        }

        var btn = scope.querySelector(".js-test-connection");
        var out = scope.querySelector(".js-test-connection-result");

        if (!btn || !out) {
            return null;
        }

        var messages = readMessages(btn);

        btn.addEventListener("click", function () {
            btn.disabled = true;
            out.className = "js-test-connection-result text-muted";
            out.textContent = messages.running;

            $.post(btn.getAttribute("data-url"))
                .done(function (data) {
                    var view = describePing(data, messages);
                    out.className = view.cssClass;
                    out.textContent = view.text;
                })
                .fail(function () {
                    out.className = "js-test-connection-result text-danger";
                    out.textContent = messages.failed;
                })
                .always(function () {
                    btn.disabled = false;
                });
        });

        return btn;
    }

    return {
        describePing: describePing,
        readMessages: readMessages,
        bindTestConnection: bindTestConnection
    };
});
