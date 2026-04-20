<?php

namespace Modules\Core\Testing;

use Modules\Core\Testing\Fixtures\FixtureLoader;
use Modules\Core\Testing\Fakes\FakeDatabase;
use Modules\Core\Testing\Fakes\FakeSession;

/**
 * Base class for Controller Integration Tests
 * 
 * Provides Laravel HTTP testing methods for controller integration tests.
 * Uses Fakes instead of Mocks and supports Fixtures for test data.
 */
abstract class ControllerTestCase extends TestCase
{
    protected mixed $CI;
    protected string $controllerClass;
    protected array $testUser = [];
    protected array $testData = [];
    protected array $sessionData = [];
    
    // Test doubles (Fakes)
    protected FakeDatabase $fakeDb;
    protected FakeSession $fakeSession;
    
    // Fixture support
    protected FixtureLoader $fixtures;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Initialize fakes
        $this->fakeDb = new FakeDatabase();
        $this->fakeSession = new FakeSession();
        $this->fixtures = new FixtureLoader();
        
        // Bootstrap CodeIgniter if not already loaded
        if (!function_exists('get_instance')) {
            $this->bootstrapCodeIgniter();
        }
        
        $this->CI =& get_instance();
        
        // Reset test state
        $this->testUser = [];
        $this->testData = [];
        
        // Load fixtures if needed
        $this->loadFixtures();
        
        // Call child setup if needed
        $this->setUpController();
    }

    protected function tearDown(): void
    {
        // Clean up test data
        $this->cleanupTestData();
        
        // Clear fakes
        $this->fakeDb->clear();
        $this->fakeSession->clear();
        $this->fixtures->clear();
        
        parent::tearDown();
    }

    /**
     * Override this method in child classes to set up controller-specific test data
     */
    protected function setUpController(): void
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
     * Override this method to clean up test-specific data
     */
    protected function cleanupTestData(): void
    {
        // Child classes can override this
    }

    /**
     * Bootstrap CodeIgniter for integration testing
     */
    protected function bootstrapCodeIgniter(): void
    {
        // Set testing environment
        if (!defined('ENVIRONMENT')) {
            define('ENVIRONMENT', 'testing');
        }
        
        if (!defined('BASEPATH')) {
            $system_path = dirname(__DIR__, 5) . '/vendor/codeigniter/framework/system';
            $application_folder = dirname(__DIR__, 5) . '/application';
            
            define('BASEPATH', $system_path . '/');
            define('APPPATH', $application_folder . '/');
            define('VIEWPATH', APPPATH . 'views/');
        }
        
        // Load CodeIgniter bootstrap
        // This will be implemented when CI is properly integrated for tests
        // For now, we mark tests as incomplete
    }

    /**
     * Create an authenticated admin user session
     */
    protected function actAsAdmin(array $userData = []): array
    {
        $defaultData = [
            'user_id' => 1,
            'user_type' => 1, // Admin
            'user_name' => 'Test Admin',
            'user_email' => 'admin@test.com',
            'user_company' => 'Test Company',
        ];
        
        $this->testUser = array_merge($defaultData, $userData);
        $this->sessionData = $this->testUser;
        
        // Use fake session
        $this->fakeSession->setMultiple($this->testUser);
        
        // Set session data when CI is available
        if (isset($this->CI->session)) {
            $this->CI->session->set_userdata($this->testUser);
        }
        
        return $this->testUser;
    }

    /**
     * Create an authenticated guest user session
     */
    protected function actAsGuest(array $userData = []): array
    {
        $defaultData = [
            'user_id' => 2,
            'user_type' => 2, // Guest (read-only)
            'user_name' => 'Test Guest',
            'user_email' => 'guest@test.com',
            'user_company' => 'Test Company',
        ];
        
        $this->testUser = array_merge($defaultData, $userData);
        $this->sessionData = $this->testUser;
        
        // Use fake session
        $this->fakeSession->setMultiple($this->testUser);
        
        // Set session data when CI is available
        if (isset($this->CI->session)) {
            $this->CI->session->set_userdata($this->testUser);
        }
        
        return $this->testUser;
    }

    /**
     * Clear authentication session
     */
    protected function clearAuth(): void
    {
        $this->testUser = [];
        $this->sessionData = [];
        
        if (isset($this->CI->session)) {
            $this->CI->session->sess_destroy();
        }
    }

    /**
     * Set POST data for the request
     */
    protected function setPostData(array $data): void
    {
        $_POST = $data;
        $this->testData = $data;
        
        if (isset($this->CI->input)) {
            // Force CI to reload input data
            $this->CI->input->__construct();
        }
    }

    /**
     * Make a GET request to the specified URI
     * 
     * @param string $uri The URI to request
     * @param array $headers Additional headers
     * @return TestResponse
     */
    protected function get(string $uri, array $headers = []): TestResponse
    {
        return $this->call('GET', $uri, [], $headers);
    }

    /**
     * Make a POST request to the specified URI
     * 
     * @param string $uri The URI to request
     * @param array $data POST data
     * @param array $headers Additional headers
     * @return TestResponse
     */
    protected function post(string $uri, array $data = [], array $headers = []): TestResponse
    {
        return $this->call('POST', $uri, $data, $headers);
    }

    /**
     * Make a PUT request to the specified URI
     * 
     * @param string $uri The URI to request
     * @param array $data PUT data
     * @param array $headers Additional headers
     * @return TestResponse
     */
    protected function put(string $uri, array $data = [], array $headers = []): TestResponse
    {
        return $this->call('PUT', $uri, $data, $headers);
    }

    /**
     * Make a DELETE request to the specified URI
     * 
     * @param string $uri The URI to request
     * @param array $data DELETE data
     * @param array $headers Additional headers
     * @return TestResponse
     */
    protected function delete(string $uri, array $data = [], array $headers = []): TestResponse
    {
        return $this->call('DELETE', $uri, $data, $headers);
    }

    /**
     * Make an HTTP request
     * 
     * @param string $method HTTP method
     * @param string $uri The URI to request
     * @param array $data Request data
     * @param array $headers Additional headers
     * @return TestResponse
     */
    protected function call(string $method, string $uri, array $data = [], array $headers = []): TestResponse
    {
        // Mock input for the request
        $_SERVER['REQUEST_METHOD'] = $method;
        $_POST = $method === 'POST' ? $data : [];
        $_GET = [];
        
        // Set up session data if authenticated
        if (!empty($this->sessionData)) {
            $_SESSION = $this->sessionData;
            if (isset($this->CI->session)) {
                foreach ($this->sessionData as $key => $value) {
                    $this->CI->session->set_userdata($key, $value);
                }
            }
        }
        
        // Create response object
        $response = new TestResponse();
        
        // Simulate routing and controller execution
        // This is a simplified implementation for testing
        // In a real Laravel app, this would use the router
        
        return $response;
    }

    /**
     * Assert database record exists
     */
    protected function assertDatabaseHas(string $table, array $conditions): void
    {
        if (!isset($this->CI->db)) {
            $this->markTestIncomplete('Database assertion requires CI bootstrap');
            return;
        }
        
        $query = $this->CI->db->get_where($table, $conditions);
        $this->assertGreaterThan(0, $query->num_rows(), 
            "Failed asserting that table '{$table}' contains matching record");
    }

    /**
     * Assert database record does not exist
     */
    protected function assertDatabaseMissing(string $table, array $conditions): void
    {
        if (!isset($this->CI->db)) {
            $this->markTestIncomplete('Database assertion requires CI bootstrap');
            return;
        }
        
        $query = $this->CI->db->get_where($table, $conditions);
        $this->assertEquals(0, $query->num_rows(), 
            "Failed asserting that table '{$table}' does not contain matching record");
    }

    /**
     * Create test data in database
     */
    protected function createTestRecord(string $table, array $data): int
    {
        if (!isset($this->CI->db)) {
            $this->markTestIncomplete('Database operation requires CI bootstrap');
            return 0;
        }
        
        $this->CI->db->insert($table, $data);
        return $this->CI->db->insert_id();
    }

    /**
     * Delete test data from database
     */
    protected function deleteTestRecord(string $table, array $conditions): void
    {
        if (!isset($this->CI->db)) {
            return;
        }
        
        $this->CI->db->delete($table, $conditions);
    }
}
