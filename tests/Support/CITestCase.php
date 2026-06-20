<?php

namespace Tests\Support;

use PHPUnit\Framework\TestCase;

/**
 * Base test case for any test that touches CodeIgniter models or helpers.
 *
 * Resets the CI test-double before every test so that state from one test
 * cannot leak into the next.  Pure-PHP unit tests that do not depend on CI3
 * can extend PHPUnit\Framework\TestCase directly.
 */
abstract class CITestCase extends TestCase
{
    protected \CITestDouble $ci;

    protected function setUp(): void
    {
        parent::setUp();

        \CITestDouble::reset();
        $this->ci = \CITestDouble::instance();
    }

    // -----------------------------------------------------------------
    // Convenience helpers
    // -----------------------------------------------------------------

    /**
     * Seed a setting value so that get_setting() / mdl_settings->setting()
     * returns the expected value inside the code under test.
     */
    protected function setSetting(string $key, mixed $value): void
    {
        \MockSettings::set($key, $value);
    }

    /**
     * Pre-load the mock DB with rows so that the next model->get()->result()
     * call returns the provided data.
     *
     * @param array<int, array<string, mixed>> $rows
     */
    protected function seedDb(array $rows): void
    {
        $this->ci->db->setRows($rows);
    }
}
