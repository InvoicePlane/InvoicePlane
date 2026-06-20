<?php

/**
 * Generic library stub returned by MockLoader::library().
 *
 * Declared as a named class (not anonymous) so that PHP can add dynamic
 * properties to it — which is required for reference-assignment chains
 * like `$this->form_validation->CI = &$this` that CI3 uses internally.
 * Anonymous classes with __set cannot be the target of = & assignment.
 */
#[AllowDynamicProperties]
class MockLibrary
{
    public function __call(string $method, array $args): mixed
    {
        return null;
    }
}
