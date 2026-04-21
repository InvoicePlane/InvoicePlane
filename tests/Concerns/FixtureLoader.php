<?php

namespace Concerns;

use RuntimeException;

/**
 * Fixture Loader.
 *
 * Provides functionality to load test fixtures from files.
 * Fixtures are reusable test data that can be loaded into tests.
 */
class FixtureLoader
{
    protected string $fixturesPath;

    protected array $loadedFixtures = [];

    public function __construct(?string $fixturesPath = null)
    {
        $this->fixturesPath = $fixturesPath ?? dirname(__DIR__, 4) . '/tests/fixtures';
    }

    /**
     * Load a fixture by name.
     *
     * @param string $name Fixture name (without .php extension)
     *
     * @return array Fixture data
     */
    public function load(string $name): array
    {
        if (isset($this->loadedFixtures[$name])) {
            return $this->loadedFixtures[$name];
        }

        $fixturePath = $this->fixturesPath . '/' . $name . '.php';

        if ( ! file_exists($fixturePath)) {
            throw new RuntimeException("Fixture file not found: {$fixturePath}");
        }

        $fixtureData = require $fixturePath;

        if ( ! is_array($fixtureData)) {
            throw new RuntimeException("Fixture must return an array: {$name}");
        }

        $this->loadedFixtures[$name] = $fixtureData;

        return $fixtureData;
    }

    /**
     * Get a specific item from a fixture.
     *
     * @param string $name Fixture name
     * @param string $key  Key to retrieve from fixture
     *
     * @return mixed Fixture item data
     */
    public function get(string $name, string $key): mixed
    {
        $fixture = $this->load($name);

        if ( ! isset($fixture[$key])) {
            throw new RuntimeException("Fixture key not found: {$name}.{$key}");
        }

        return $fixture[$key];
    }

    /**
     * Get all items from a fixture.
     *
     * @param string $name Fixture name
     *
     * @return array All fixture items
     */
    public function all(string $name): array
    {
        return $this->load($name);
    }

    /**
     * Clear loaded fixtures from memory.
     */
    public function clear(): void
    {
        $this->loadedFixtures = [];
    }

    /**
     * Set custom fixtures path.
     */
    public function setFixturesPath(string $path): void
    {
        $this->fixturesPath = $path;
        $this->clear();
    }
}
