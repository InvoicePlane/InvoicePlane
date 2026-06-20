<?php

/**
 * Minimal CI3 loader mock.
 *
 * Stubs the most common load calls used by models and helpers.
 * Loaded libraries are attached to the CI test-double as anonymous-class
 * mocks so that property-assignment chains like
 * `$this->form_validation->CI = &$this` don't throw.
 */
class MockLoader
{
    private object $ci;

    public function __construct(object $ci)
    {
        $this->ci = $ci;
    }

    // -----------------------------------------------------------------
    // CI Loader API stubs
    // -----------------------------------------------------------------

    public function library(string|array $library, mixed $params = null, ?string $object_name = null): void
    {
        $libs = is_array($library) ? $library : [$library];

        foreach ($libs as $lib) {
            $name = $object_name ?? strtolower((string) $lib);
            if (! isset($this->ci->{$name})) {
                $this->ci->{$name} = $this->makeNullMock();
            }
        }
    }

    public function model(string|array $model, ?string $object_name = null, bool $db_conn = false): void
    {
        // No-op: models are instantiated directly in tests when needed.
    }

    public function helper(string|array $helper): void
    {
        // No-op: helpers that tests need should be required explicitly in the bootstrap.
    }

    public function database(mixed $params = '', bool $return = false, ?bool $query_builder = null): ?MockDB
    {
        if ($return) {
            return new MockDB();
        }

        return null;
    }

    public function view(string $view, array $vars = [], bool $return = false): string
    {
        return '';
    }

    public function config(string $file, bool $use_sections = false, bool $fail_gracefully = false): void {}

    public function initialize(): void {}

    // -----------------------------------------------------------------
    // Internal
    // -----------------------------------------------------------------

    private function makeNullMock(): MockLibrary
    {
        return new MockLibrary();
    }
}
