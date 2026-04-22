<?php

namespace Modules\Core\Testing;

use RuntimeException;
use Tests\AbstractTestCase;

/**
 * HttpTestCase - Base class for HTTP integration tests.
 *
 * Provides methods for making HTTP requests and handling authentication
 * in CodeIgniter 3 tests.
 */
abstract class HttpTestCase extends AbstractTestCase
{
    protected mixed $codeigniter;

    protected array $sessionData = [];

    protected ?int $authenticatedUserId = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->setupDatabase();
        $this->codeigniter = &get_instance();
    }

    /**
     * Clean up after tests.
     */
    protected function tearDown(): void
    {
        $this->clearAuthentication();
        parent::tearDown();
    }

    /**
     * Setup test database (to be implemented based on environment).
     */
    protected function setupDatabase(): void
    {
        // Override in subclasses to setup database fixtures
        // Can run SQL migration files or use factories
    }

    /**
     * Create an authenticated admin user for testing.
     */
    protected function actingAsAdmin(array $userData = []): int
    {
        return $this->actingAs(array_merge(['user_type' => 1], $userData));
    }

    /**
     * Create an authenticated guest user for testing.
     */
    protected function actingAsGuest(array $userData = []): int
    {
        return $this->actingAs(array_merge(['user_type' => 2], $userData));
    }

    /**
     * Create and authenticate as a user.
     */
    protected function actingAs(array $userData = []): int
    {
        // Generate consistent email if not provided
        if ( ! isset($userData['user_email'])) {
            $userData['user_email'] = 'test' . bin2hex(random_bytes(8)) . '@example.com';
        }

        $userId = $this->createUser($userData);

        // Set session data using the same email
        $this->sessionData = [
            'user_id'       => $userId,
            'user_type'     => $userData['user_type'] ?? 1,
            'user_email'    => $userData['user_email'],
            'user_name'     => $userData['user_name'] ?? 'Test User',
            'user_company'  => $userData['user_company'] ?? 'Test Company',
            'user_language' => $userData['user_language'] ?? 'system',
        ];

        $this->authenticatedUserId = $userId;

        return $userId;
    }

    /**
     * Clear authentication.
     */
    protected function clearAuthentication(): void
    {
        $this->sessionData         = [];
        $this->authenticatedUserId = null;
    }

    /**
     * Create a user in the database.
     */
    protected function createUser(array $data = []): int
    {
        $ci = &get_instance();
        $ci->load->library('crypt');

        $defaults = [
            'user_type'          => 1,
            'user_email'         => 'test' . bin2hex(random_bytes(8)) . '@example.com',
            'user_name'          => 'Test User',
            'user_company'       => 'Test Company',
            'user_active'        => 1,
            'user_language'      => 'system',
            'user_date_created'  => date('Y-m-d H:i:s'),
            'user_date_modified' => date('Y-m-d H:i:s'),
        ];

        $userData = array_merge($defaults, $data);

        // Hash password
        $password                  = $data['password'] ?? 'password';
        $salt                      = $ci->crypt->salt();
        $userData['user_psalt']    = $salt;
        $userData['user_password'] = $ci->crypt->generate_password($password, $salt);
        unset($userData['password']);

        $ci->db->insert('ip_users', $userData);

        return (int) $ci->db->insert_id();
    }

    /**
     * Make a GET request.
     */
    protected function get(string $uri, array $query = []): TestResponse
    {
        return $this->call('GET', $uri, [], $query);
    }

    /**
     * Make a POST request.
     */
    protected function post(string $uri, array $data = []): TestResponse
    {
        return $this->call('POST', $uri, $data);
    }

    /**
     * Make an HTTP request.
     */
    protected function call(string $method, string $uri, array $data = [], array $query = []): TestResponse
    {
        // Mock CodeIgniter's input and session for the request
        $this->mockInput($method, $data, $query);
        $this->mockSession();

        // Parse URI and route to controller
        return $this->routeRequest($method, $uri, $data);
    }

    /**
     * Mock CodeIgniter's input.
     */
    protected function mockInput(string $method, array $data, array $query): void
    {
        $_SERVER['REQUEST_METHOD'] = $method;
        $_POST                     = $method === 'POST' ? $data : [];
        $_GET                      = $query;
    }

    /**
     * Mock CodeIgniter's session with authenticated user data.
     */
    protected function mockSession(): void
    {
        if (empty($this->sessionData)) {
            return;
        }

        $ci = &get_instance();
        if (isset($ci->session)) {
            foreach ($this->sessionData as $key => $value) {
                $ci->session->set_userdata($key, $value);
            }
        }
    }

    /**
     * Route the request to appropriate controller/method.
     */
    protected function routeRequest(string $method, string $uri, array $data): TestResponse
    {
        // Routing is not yet implemented for HTTP integration tests.
        // Failing fast here avoids misleading test results (e.g. always-200 responses).
        throw new RuntimeException(
            sprintf(
                'HttpTestCase::routeRequest is not implemented. Attempted to route [%s] %s.',
                $method,
                $uri
            )
        );
    }

    /**
     * Assert response status is 200.
     */
    protected function assertOk(TestResponse $response, string $message = ''): void
    {
        $this->assertEquals(200, $response->getStatusCode(), $message ?: 'Expected status 200');
    }

    /**
     * Assert response is a redirect.
     */
    protected function assertRedirect(TestResponse $response, ?string $expectedLocation = null): void
    {
        $this->assertTrue(
            in_array($response->getStatusCode(), [301, 302, 303, 307, 308]),
            'Expected redirect status code'
        );

        if ($expectedLocation !== null) {
            $this->assertStringContainsString($expectedLocation, $response->getRedirectUrl());
        }
    }

    /**
     * Assert response is unauthorized.
     */
    protected function assertUnauthorized(TestResponse $response): void
    {
        $this->assertTrue(
            in_array($response->getStatusCode(), [401, 403]),
            'Expected unauthorized status code (401 or 403)'
        );
    }

    /**
     * Assert response contains text.
     */
    protected function assertResponseContains(TestResponse $response, string $needle): void
    {
        $this->assertStringContainsString($needle, $response->getContent());
    }

    /**
     * Assert response JSON matches.
     */
    protected function assertJsonResponse(TestResponse $response, array $expected): void
    {
        $actual = json_decode($response->getContent(), true);
        $this->assertEquals($expected, $actual);
    }

    /**
     * Assert database has record.
     */
    protected function assertDatabaseHas(string $table, array $conditions): void
    {
        $ci    = &get_instance();
        $query = $ci->db->get_where($table, $conditions);
        $this->assertGreaterThan(0, $query->num_rows(), "Expected record in table {$table}");
    }

    /**
     * Assert database missing record.
     */
    protected function assertDatabaseMissing(string $table, array $conditions): void
    {
        $ci    = &get_instance();
        $query = $ci->db->get_where($table, $conditions);
        $this->assertEquals(0, $query->num_rows(), "Expected no record in table {$table}");
    }

    /**
     * Get count of records in database.
     */
    protected function getDatabaseCount(string $table, array $conditions = []): int
    {
        $ci = &get_instance();
        if (empty($conditions)) {
            return $ci->db->count_all($table);
        }

        return $ci->db->where($conditions)->count_all_results($table);
    }
}
