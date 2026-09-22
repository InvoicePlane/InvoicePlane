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

import { test } from '../test.js';

const NEEDS_CSRF_ON = 'needs a CSRF_PROTECTION=true server — covered by CsrfMutationParityTest';

for (const title of [
  'it deletes an import batch with a valid csrf token',
  'it does not delete an import batch without a csrf token',
  'it unassigns a client from a user with a valid csrf token',
  'it does not unassign a client from a user without a csrf token',
  'it deletes a payment with a valid csrf token',
  'it does not delete a payment without a csrf token',
  'it recalculates invoice amounts with a valid csrf token',
  'it does not recalculate invoice amounts without a csrf token',
  'it recalculates quote amounts with a valid csrf token',
  'it does not recalculate quote amounts without a csrf token',
  'it changes a password via reset with a valid csrf token',
  'it does not change a password via reset without a csrf token',
]) {
  test(title, () => {
    test.fixme(true, NEEDS_CSRF_ON);
  });
}
