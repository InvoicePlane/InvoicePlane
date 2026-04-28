<?php

namespace Modules\Core\Testing;

use Modules\Core\Testing\Fakes\FakeDatabase;
use Modules\Core\Testing\Fixtures\FixtureLoader;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use ReflectionClass;

/**
 * Base class for Model Unit Tests.
 *
 * Provides utilities for testing model classes in isolation.
 * Models are tested as unit tests with Fakes (not Mocks) for dependencies.
 * Supports Fixtures for reusable test data.
 */
abstract class ModelTestCase extends PHPUnitTestCase
{
    protected mixed $model;

    protected string $modelClass;

    protected array $testData = [];

    // Test doubles (Fakes)
    protected FakeDatabase $fakeDb;

    // Fixture support
    protected FixtureLoader $fixtures;

    protected function setUp(): void
    {
        parent::setUp();

        // Initialize fakes
        $this->fakeDb   = new FakeDatabase();
        $this->fixtures = new FixtureLoader();

        // Reset test state
        $this->testData = [];

        // Load fixtures if needed
        $this->loadFixtures();

        // Call child setup
        $this->setUpModel();
    }

    protected function tearDown(): void
    {
        $this->model = null;

        // Clear fakes
        $this->fakeDb->clear();
        $this->fixtures->clear();

        parent::tearDown();
    }

    /**
     * Override this method in child classes to set up model-specific test data.
     */
    protected function setUpModel(): void
    {
        // Child classes can override this
    }

    /**
     * Override this method to load fixtures.
     */
    protected function loadFixtures(): void
    {
        // Child classes can override this to load specific fixtures
    }

    /**
     * Get the model instance for testing.
     */
    protected function getModel(): mixed
    {
        if ($this->model === null && ! empty($this->modelClass)) {
            $this->model = new $this->modelClass();
        }

        return $this->model;
    }

    /**
     * Get the fake database (preferred over mocks).
     */
    protected function getFakeDb(): FakeDatabase
    {
        return $this->fakeDb;
    }

    /**
     * Assert that a SQL query contains expected elements.
     */
    protected function assertQueryContains(string $query, string $expected): void
    {
        $this->assertStringContainsString(
            $expected,
            $query,
            "Expected SQL query to contain '{$expected}'"
        );
    }

    /**
     * Assert that a SQL query is a SELECT statement.
     */
    protected function assertIsSelectQuery(string $query): void
    {
        $this->assertMatchesRegularExpression(
            '/^\s*SELECT\s+/i',
            $query,
            'Expected query to be a SELECT statement'
        );
    }

    /**
     * Assert that a SQL query is an INSERT statement.
     */
    protected function assertIsInsertQuery(string $query): void
    {
        $this->assertMatchesRegularExpression(
            '/^\s*INSERT\s+INTO\s+/i',
            $query,
            'Expected query to be an INSERT statement'
        );
    }

    /**
     * Assert that a SQL query is an UPDATE statement.
     */
    protected function assertIsUpdateQuery(string $query): void
    {
        $this->assertMatchesRegularExpression(
            '/^\s*UPDATE\s+/i',
            $query,
            'Expected query to be an UPDATE statement'
        );
    }

    /**
     * Assert that a SQL query is a DELETE statement.
     */
    protected function assertIsDeleteQuery(string $query): void
    {
        $this->assertMatchesRegularExpression(
            '/^\s*DELETE\s+FROM\s+/i',
            $query,
            'Expected query to be a DELETE statement'
        );
    }

    /**
     * Assert that validation rules exist for a field.
     */
    protected function assertHasValidationRule(array $rules, string $field): void
    {
        $fieldNames = array_column($rules, 'field');
        $this->assertContains(
            $field,
            $fieldNames,
            "Expected validation rules to contain field '{$field}'"
        );
    }

    /**
     * Assert that a field is required in validation rules.
     */
    protected function assertFieldIsRequired(array $rules, string $field): void
    {
        $this->assertHasValidationRule($rules, $field);

        foreach ($rules as $rule) {
            if ($rule['field'] === $field) {
                $this->assertStringContainsString(
                    'required',
                    $rule['rules'],
                    "Expected field '{$field}' to be required"
                );

                return;
            }
        }
    }

    /**
     * Assert that a method exists on the model.
     */
    protected function assertModelHasMethod(string $method): void
    {
        $this->assertTrue(
            method_exists($this->getModel(), $method),
            "Model does not have method '{$method}'"
        );
    }

    /**
     * Assert that a property exists on the model.
     */
    protected function assertModelHasProperty(string $property): void
    {
        $this->assertTrue(
            property_exists($this->getModel(), $property),
            "Model does not have property '{$property}'"
        );
    }

    /**
     * Assert that model uses the correct table.
     */
    protected function assertUsesTable(string $expectedTable): void
    {
        $model = $this->getModel();

        if ( ! property_exists($model, 'table')) {
            $this->fail('Model does not have a table property');
        }

        $this->assertEquals(
            $expectedTable,
            $model->table,
            "Expected model to use table '{$expectedTable}'"
        );
    }

    /**
     * Assert that model has correct primary key.
     */
    protected function assertHasPrimaryKey(string $expectedKey): void
    {
        $model = $this->getModel();

        if ( ! property_exists($model, 'primary_key')) {
            $this->fail('Model does not have a primary_key property');
        }

        $this->assertEquals(
            $expectedKey,
            $model->primary_key,
            "Expected model to have primary key '{$expectedKey}'"
        );
    }

    /**
     * Get validation rules from model.
     */
    protected function getValidationRules(string $rulesMethod = 'validation_rules'): array
    {
        $model = $this->getModel();

        if ( ! method_exists($model, $rulesMethod)) {
            $this->fail("Model does not have method '{$rulesMethod}'");
        }

        return $model->{$rulesMethod}();
    }

    /**
     * Assert that a scope method exists.
     */
    protected function assertHasScope(string $scopeName): void
    {
        $this->assertModelHasMethod($scopeName);
    }

    /**
     * Call a protected/private method on the model for testing.
     */
    protected function invokeMethod(string $methodName, array $parameters = []): mixed
    {
        $reflection = new ReflectionClass($this->getModel());
        $method     = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($this->getModel(), $parameters);
    }

    /**
     * Get a protected/private property value from the model.
     */
    protected function getProperty(string $propertyName): mixed
    {
        $reflection = new ReflectionClass($this->getModel());
        $property   = $reflection->getProperty($propertyName);
        $property->setAccessible(true);

        return $property->getValue($this->getModel());
    }

    /**
     * Set a protected/private property value on the model.
     */
    protected function setProperty(string $propertyName, mixed $value): void
    {
        $reflection = new ReflectionClass($this->getModel());
        $property   = $reflection->getProperty($propertyName);
        $property->setAccessible(true);

        $property->setValue($this->getModel(), $value);
    }
}
