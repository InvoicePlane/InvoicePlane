<?php

namespace InvoicePlane\Tests\Generated;

use PHPUnit\Framework\TestCase;

/**
 * Generated test class from InvoicePlane interaction recording
 * 
 * Session ID: ip_example_abc123
 * Generated: 2025-09-30T06:09:33.630Z
 * Duration: 40000ms
 * Events: 11
 * 
 * Original URL: http://localhost:8080/
 * User Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36
 */
class RecordedTest_ip_example_abc123_20250930T060933629Z extends TestCase
{
    protected static $baseUrl = 'http://localhost:8080';
    protected $client;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        // Initialize HTTP client for testing
        $this->client = new \GuzzleHttp\Client([
            'base_uri' => self::$baseUrl,
            'timeout' => 30,
            'allow_redirects' => true,
            'cookies' => true
        ]);
    }
    
    public function testRecordedInteraction()
    {
        // Test generated from recorded interaction
        // Session: ip_example_abc123
        
        // Step 1: click
        // Click on element: a:has-text("Enter")
        $response = $this->client->get('/sessions/login');
        $this->assertEquals(200, $response->getStatusCode());
        
        // Step 2: navigation
        $response = $this->client->get('/sessions/login');
        $this->assertEquals(200, $response->getStatusCode());
        $html = $response->getBody()->getContents();
        
        // Step 3: form_submit
        // Submit form: #login-form
        $formData = [
            'email' => 'admin@example.com',
            '_ip_csrf' => 'abc123csrf456'
        ];
        
        // Add CSRF token
        $csrfToken = $this->extractCSRFToken($html);
        if ($csrfToken) {
            $formData['abc123csrf456'] = $csrfToken;
        }
        
        $response = $this->submitForm('/sessions/login', $formData, 'POST');
        $this->assertTrue(in_array($response->getStatusCode(), [200, 201, 302]));
        
        // Step 4: navigation
        $response = $this->client->get('/dashboard');
        $this->assertEquals(200, $response->getStatusCode());
        $html = $response->getBody()->getContents();
        
        // Step 5: click
        // Click on element: .create-invoice
        // Verify element exists: .create-invoice
        $this->assertTrue($this->findElementBySelector($html, '.create-invoice'));
        
        // Step 6: ajax_request
        // AJAX request: GET http://localhost:8080/invoices/ajax/modal_create_invoice
        $response = $this->client->request('GET', '/invoices/ajax/modal_create_invoice');
        $this->assertEquals(200, $response->getStatusCode());
        
    }
    
    /**
     * Helper method to extract CSRF token from response
     */
    protected function extractCSRFToken(string $html): ?string
    {
        $pattern = '/name="[^"]*csrf[^"]*"[^>]*value="([^"]+)"/i';
        if (preg_match($pattern, $html, $matches)) {
            return $matches[1];
        }
        return null;
    }
    
    /**
     * Helper method to find element by selector in HTML
     */
    protected function findElementBySelector(string $html, string $selector): bool
    {
        // Basic selector parsing for testing
        if (strpos($selector, '#') === 0) {
            $id = substr($selector, 1);
            return strpos($html, "id="{$id}"") !== false;
        }
        
        if (strpos($selector, '.') === 0) {
            $class = substr($selector, 1);
            return strpos($html, "class="{$class}"") !== false || 
                   strpos($html, "class="{$class} ") !== false ||
                   strpos($html, " {$class}"") !== false;
        }
        
        return strpos($html, $selector) !== false;
    }
    
    /**
     * Helper method to simulate form submission
     */
    protected function submitForm(string $url, array $formData, string $method = 'POST'): \Psr\Http\Message\ResponseInterface
    {
        $options = [];
        
        if (strtoupper($method) === 'POST') {
            $options['form_params'] = $formData;
        } else {
            $options['query'] = $formData;
        }
        
        return $this->client->request($method, $url, $options);
    }
}
