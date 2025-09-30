const fs = require('fs');
const path = require('path');

/**
 * Converts recorded interaction JSON files to PHPUnit test cases
 * Generates CodeIgniter-compatible test files for InvoicePlane
 */

class RecordingToPHPUnitConverter {
    constructor(options = {}) {
        this.options = {
            outputDir: 'tests/generated',
            namespace: 'InvoicePlane\\Tests\\Generated',
            baseUrl: 'http://localhost:8080',
            ...options
        };
    }
    
    /**
     * Convert a recording JSON file to PHPUnit test
     */
    async convertRecording(recordingPath) {
        console.log(`🔄 Converting recording: ${recordingPath}`);
        
        const recordingData = JSON.parse(fs.readFileSync(recordingPath, 'utf8'));
        const testClass = this.generateTestClass(recordingData);
        
        const outputFileName = this.getOutputFileName(recordingPath);
        const outputPath = path.join(this.options.outputDir, outputFileName);
        
        // Ensure output directory exists
        if (!fs.existsSync(this.options.outputDir)) {
            fs.mkdirSync(this.options.outputDir, { recursive: true });
        }
        
        fs.writeFileSync(outputPath, testClass);
        
        console.log(`✅ Generated PHPUnit test: ${outputPath}`);
        return outputPath;
    }
    
    /**
     * Generate PHPUnit test class from recording data
     */
    generateTestClass(recording) {
        const className = this.generateClassName(recording);
        const testMethods = this.generateTestMethods(recording);
        
        return `<?php

namespace ${this.options.namespace};

use PHPUnit\\Framework\\TestCase;

/**
 * Generated test class from InvoicePlane interaction recording
 * 
 * Session ID: ${recording.sessionId}
 * Generated: ${new Date().toISOString()}
 * Duration: ${recording.duration}ms
 * Events: ${recording.events.length}
 * 
 * Original URL: ${recording.metadata?.url || 'Unknown'}
 * User Agent: ${recording.metadata?.userAgent || 'Unknown'}
 */
class ${className} extends TestCase
{
    protected static $baseUrl = '${this.options.baseUrl}';
    protected $client;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        // Initialize HTTP client for testing
        $this->client = new \\GuzzleHttp\\Client([
            'base_uri' => self::$baseUrl,
            'timeout' => 30,
            'allow_redirects' => true,
            'cookies' => true
        ]);
    }
    
${testMethods}
    
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
            return strpos($html, "id=\"{$id}\"") !== false;
        }
        
        if (strpos($selector, '.') === 0) {
            $class = substr($selector, 1);
            return strpos($html, "class=\"{$class}\"") !== false || 
                   strpos($html, "class=\"{$class} ") !== false ||
                   strpos($html, " {$class}\"") !== false;
        }
        
        return strpos($html, $selector) !== false;
    }
    
    /**
     * Helper method to simulate form submission
     */
    protected function submitForm(string $url, array $formData, string $method = 'POST'): \\Psr\\Http\\Message\\ResponseInterface
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
`;
    }
    
    /**
     * Generate test methods from recording events
     */
    generateTestMethods(recording) {
        const events = recording.events.filter(event => 
            ['click', 'form_submit', 'navigation', 'ajax_request'].includes(event.type)
        );
        
        if (events.length === 0) {
            return `    public function testRecordedInteraction()
    {
        $this->markTestSkipped('No recordable events found in recording');
    }`;
        }
        
        let testMethod = `    public function testRecordedInteraction()
    {
        // Test generated from recorded interaction
        // Session: ${recording.sessionId}
        
`;
        
        let stepNumber = 1;
        let currentUrl = null;
        let csrfToken = null;
        
        for (const event of events) {
            testMethod += `        // Step ${stepNumber}: ${event.type}
`;
            
            switch (event.type) {
                case 'navigation':
                    currentUrl = event.data.url;
                    testMethod += `        $response = $this->client->get('${this.getRelativeUrl(currentUrl)}');
        $this->assertEquals(200, $response->getStatusCode());
        $html = $response->getBody()->getContents();
        
`;
                    break;
                    
                case 'click':
                    const clickData = event.data;
                    testMethod += `        // Click on element: ${clickData.selector}
`;
                    
                    if (clickData.href) {
                        testMethod += `        $response = $this->client->get('${this.getRelativeUrl(clickData.href)}');
        $this->assertEquals(200, $response->getStatusCode());
        
`;
                    } else {
                        testMethod += `        // Verify element exists: ${clickData.selector}
        $this->assertTrue($this->findElementBySelector($html, '${this.escapeSelector(clickData.selector)}'));
        
`;
                    }
                    break;
                    
                case 'form_submit':
                    const formData = event.data;
                    testMethod += `        // Submit form: ${formData.selector}
        $formData = ${this.formatPHPArray(formData.formData || {})};
        
`;
                    
                    if (formData.csrfToken) {
                        testMethod += `        // Add CSRF token
        $csrfToken = $this->extractCSRFToken($html);
        if ($csrfToken) {
            $formData['${formData.csrfToken}'] = $csrfToken;
        }
        
`;
                    }
                    
                    const submitUrl = formData.action || currentUrl;
                    const method = formData.method || 'POST';
                    
                    testMethod += `        $response = $this->submitForm('${this.getRelativeUrl(submitUrl)}', $formData, '${method.toUpperCase()}');
        $this->assertTrue(in_array($response->getStatusCode(), [200, 201, 302]));
        
`;
                    break;
                    
                case 'ajax_request':
                    const ajaxData = event.data;
                    testMethod += `        // AJAX request: ${ajaxData.method} ${ajaxData.url}
        $response = $this->client->request('${ajaxData.method}', '${this.getRelativeUrl(ajaxData.url)}');
        $this->assertEquals(${ajaxData.status}, $response->getStatusCode());
        
`;
                    break;
            }
            
            stepNumber++;
        }
        
        testMethod += `    }`;
        
        return testMethod;
    }
    
    /**
     * Generate class name from recording
     */
    generateClassName(recording) {
        const sessionId = recording.sessionId || 'Unknown';
        const timestamp = new Date().toISOString().replace(/[^\w]/g, '');
        return `RecordedTest_${sessionId}_${timestamp}`;
    }
    
    /**
     * Get output file name for recording
     */
    getOutputFileName(recordingPath) {
        const baseName = path.basename(recordingPath, '.json');
        return `${baseName}Test.php`;
    }
    
    /**
     * Convert absolute URL to relative path
     */
    getRelativeUrl(url) {
        if (!url) return '/';
        
        try {
            const urlObj = new URL(url);
            return urlObj.pathname + urlObj.search + urlObj.hash;
        } catch (e) {
            return url.startsWith('/') ? url : '/' + url;
        }
    }
    
    /**
     * Escape selector for PHP string
     */
    escapeSelector(selector) {
        return selector.replace(/\\/g, '\\\\').replace(/'/g, "\\'");
    }
    
    /**
     * Format PHP array from JavaScript object
     */
    formatPHPArray(obj) {
        if (!obj || typeof obj !== 'object') {
            return '[]';
        }
        
        const entries = Object.entries(obj)
            .filter(([key, value]) => value !== '[FILTERED]') // Skip filtered values
            .map(([key, value]) => {
                const phpKey = `'${key.replace(/'/g, "\\'")}'`;
                let phpValue;
                
                if (typeof value === 'string') {
                    phpValue = `'${value.replace(/'/g, "\\'")}'`;
                } else if (typeof value === 'number') {
                    phpValue = value.toString();
                } else if (typeof value === 'boolean') {
                    phpValue = value ? 'true' : 'false';
                } else {
                    phpValue = `'${String(value).replace(/'/g, "\\'")}'`;
                }
                
                return `            ${phpKey} => ${phpValue}`;
            });
        
        if (entries.length === 0) {
            return '[]';
        }
        
        return `[
${entries.join(',\n')}
        ]`;
    }
}

/**
 * Command line interface
 */
async function main() {
    const args = process.argv.slice(2);
    
    if (args.length === 0) {
        console.log(`
Usage: node convert-to-phpunit.js <recording-file> [options]

Options:
  --output-dir=<dir>    Output directory for generated tests (default: tests/generated)
  --base-url=<url>      Base URL for the application (default: http://localhost:8080)

Examples:
  node convert-to-phpunit.js tests/recordings/recording-123.json
  node convert-to-phpunit.js tests/recordings/recording-123.json --output-dir=tests/unit --base-url=https://demo.invoiceplane.com
        `);
        process.exit(1);
    }
    
    const recordingFile = args[0];
    const outputDir = args.find(arg => arg.startsWith('--output-dir='))?.split('=')[1] || 'tests/generated';
    const baseUrl = args.find(arg => arg.startsWith('--base-url='))?.split('=')[1] || 'http://localhost:8080';
    
    if (!fs.existsSync(recordingFile)) {
        console.error(`❌ Recording file not found: ${recordingFile}`);
        process.exit(1);
    }
    
    const converter = new RecordingToPHPUnitConverter({
        outputDir,
        baseUrl
    });
    
    try {
        const outputPath = await converter.convertRecording(recordingFile);
        console.log(`
✅ Conversion completed successfully!

Generated test file: ${outputPath}

To run the test:
1. Make sure you have PHPUnit installed
2. Configure your test database
3. Run: vendor/bin/phpunit ${outputPath}
        `);
    } catch (error) {
        console.error('❌ Conversion failed:', error);
        process.exit(1);
    }
}

if (require.main === module) {
    main();
}

module.exports = { RecordingToPHPUnitConverter };