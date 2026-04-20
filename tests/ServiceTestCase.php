<?php

namespace Modules\Core\Testing;

use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use Modules\Core\Testing\Fixtures\FixtureLoader;
use Modules\Core\Testing\Fakes\FakeDatabase;

/**
 * Base class for Service/Model Unit Tests
 * 
 * Provides utilities for testing service classes (models) in isolation.
 * Services are tested as unit tests with Fakes (not Mocks) for dependencies.
 * Supports Fixtures for reusable test data.
 */
abstract class ServiceTestCase extends PHPUnitTestCase
{
    protected mixed $service;
    protected string $serviceClass;
    protected array $testData = [];
    
    // Test doubles (Fakes)
    protected FakeDatabase $fakeDb;
    
    // Fixture support
    protected FixtureLoader $fixtures;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Initialize fakes
        $this->fakeDb = new FakeDatabase();
        $this->fixtures = new FixtureLoader();
        
        // Reset test state
        $this->testData = [];
        
        // Load fixtures if needed
        $this->loadFixtures();
        
        // Call child setup
        $this->setUpService();
    }

    protected function tearDown(): void
    {
        $this->service = null;
        
        // Clear fakes
        $this->fakeDb->clear();
        $this->fixtures->clear();
        
        parent::tearDown();
    }

    /**
     * Override this method in child classes to set up service-specific test data
     */
    protected function setUpService(): void
    {
        // Child classes can override this
    }

    /**
     * Override this method to load fixtures
     */
    protected function loadFixtures(): void
    {
        // Child classes can override this to load specific fixtures
    }

    /**
     * Get the service instance for testing
     */
    protected function getService(): mixed
    {
        if ($this->service === null && !empty($this->serviceClass)) {
            $this->service = new $this->serviceClass();
        }
        
        return $this->service;
    }

    /**
     * Get the fake database (preferred over mocks)
     */
    protected function getFakeDb(): FakeDatabase
    {
        return $this->fakeDb;
    }

    /**
     * Assert that a SQL query contains expected elements
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
     * Assert that a SQL query is a SELECT statement
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
     * Assert that a SQL query is an INSERT statement
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
     * Assert that a SQL query is an UPDATE statement
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
     * Assert that a SQL query is a DELETE statement
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
     * Assert that validation rules exist for a field
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
     * Assert that a field is required in validation rules
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
     * Assert that a method exists on the service
     */
    protected function assertServiceHasMethod(string $method): void
    {
        $this->assertTrue(
            method_exists($this->getService(), $method),
            "Service does not have method '{$method}'"
        );
    }

    /**
     * Assert that a property exists on the service
     */
    protected function assertServiceHasProperty(string $property): void
    {
        $this->assertTrue(
            property_exists($this->getService(), $property),
            "Service does not have property '{$property}'"
        );
    }

    /**
     * Assert that service uses the correct table
     */
    protected function assertUsesTable(string $expectedTable): void
    {
        $service = $this->getService();
        
        if (!property_exists($service, 'table')) {
            $this->fail('Service does not have a table property');
        }
        
        $this->assertEquals(
            $expectedTable,
            $service->table,
            "Expected service to use table '{$expectedTable}'"
        );
    }

    /**
     * Assert that service has correct primary key
     */
    protected function assertHasPrimaryKey(string $expectedKey): void
    {
        $service = $this->getService();
        
        if (!property_exists($service, 'primary_key')) {
            $this->fail('Service does not have a primary_key property');
        }
        
        $this->assertEquals(
            $expectedKey,
            $service->primary_key,
            "Expected service to have primary key '{$expectedKey}'"
        );
    }

    /**
     * Get validation rules from service
     */
    protected function getValidationRules(string $rulesMethod = 'validation_rules'): array
    {
        $service = $this->getService();
        
        if (!method_exists($service, $rulesMethod)) {
            $this->fail("Service does not have method '{$rulesMethod}'");
        }
        
        return $service->$rulesMethod();
    }

    /**
     * Assert that a scope method exists
     */
    protected function assertHasScope(string $scopeName): void
    {
        $this->assertServiceHasMethod($scopeName);
    }

    /**
     * Call a protected/private method on the service for testing
     */
    protected function invokeMethod(string $methodName, array $parameters = []): mixed
    {
        $reflection = new \ReflectionClass($this->getService());
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);
        
        return $method->invokeArgs($this->getService(), $parameters);
    }

    /**
     * Get a protected/private property value from the service
     */
    protected function getProperty(string $propertyName): mixed
    {
        $reflection = new \ReflectionClass($this->getService());
        $property = $reflection->getProperty($propertyName);
        $property->setAccessible(true);
        
        return $property->getValue($this->getService());
    }

    /**
     * Set a protected/private property value on the service
     */
    protected function setProperty(string $propertyName, mixed $value): void
    {
        $reflection = new \ReflectionClass($this->getService());
        $property = $reflection->getProperty($propertyName);
        $property->setAccessible(true);
        
        $property->setValue($this->getService(), $value);
    }
}
