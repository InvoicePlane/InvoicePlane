/**
 * tests/Feature/Core/CsrfMutationParityTest.php pins that a set of
 * state-changing endpoints behave identically under the PRODUCTION config
 * (CSRF on): the request succeeds with a valid token and is refused without
 * one.
 *
 * This E2E server runs with CSRF_PROTECTION=false (local ipconfig + the CI
 * workflow), so the "without a token" halves cannot be exercised here — they
 * would simply succeed. The parity guarantee stays covered by the PHPUnit
 * Feature suite (and the config-parity-guard skill). Wiring a second Playwright
 * project against a CSRF-on server would let these run here too.
 */

/* All CSRF mutation parity tests require CSRF_PROTECTION=true server.
   Fully covered by tests/Feature/Core/CsrfMutationParityTest.php. */
