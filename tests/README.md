# InvoicePlane Advanced Testing System

This directory contains the advanced testing infrastructure for InvoicePlane, featuring a Playwright-based interaction recording system that can convert user interactions into PHPUnit tests.

## Overview

The advanced testing system consists of several components:

- **Advanced Recorder (`advanced-recording.js`)**: A client-side JavaScript recorder that captures user interactions
- **Playwright Integration**: Browser automation for test recording and execution
- **JSON-to-PHPUnit Converter**: Converts recorded interactions to PHPUnit test cases
- **Integration Scripts**: Helpers to integrate the recorder with the existing InvoicePlane codebase

## Features

✅ **Interaction Recording**: Records clicks, form submissions, navigation, and AJAX requests  
✅ **Smart Selectors**: Generates robust CSS selectors for reliable element identification  
✅ **CSRF Token Handling**: Automatically handles InvoicePlane's CSRF protection  
✅ **Form Data Capture**: Records form inputs while filtering sensitive data  
✅ **Modal and UI Integration**: Recognizes InvoicePlane's modals and UI patterns  
✅ **PHPUnit Generation**: Converts recordings to executable PHPUnit tests  
✅ **Real Browser Testing**: Uses Playwright for realistic browser automation  

## Directory Structure

```
tests/
├── README.md                          # This file
├── playwright/                        # Playwright test files
│   ├── record-interactions.js         # Interactive recording script
│   ├── convert-to-phpunit.js         # JSON to PHPUnit converter
│   ├── integrate-recorder.js         # Integration helper
│   └── example.spec.js               # Example Playwright tests
├── recordings/                        # Saved interaction recordings (JSON)
└── generated/                         # Generated PHPUnit test files
```

## Setup

### 1. Install Dependencies

```bash
# Install Node.js dependencies (including Playwright)
yarn install

# Install Playwright browsers
npx playwright install
```

### 2. Integrate the Recorder

```bash
# Integrate the recorder with InvoicePlane's main scripts
node tests/playwright/integrate-recorder.js integrate
```

This will:
- Add the recorder initialization code to `assets/core/js/scripts.js`
- Update the Gruntfile to preserve the recorder during builds
- Create a backup of the original scripts file

### 3. Start InvoicePlane

Make sure InvoicePlane is running locally:

```bash
# Using PHP's built-in server
php -S localhost:8080

# Or using your preferred web server
```

## Usage

### Recording Interactions

#### Method 1: Interactive Recording Session

Start a guided recording session:

```bash
npm run test:record
```

This will:
1. Open a browser window with InvoicePlane
2. Enable the recorder automatically
3. Allow you to interact with the application manually
4. Save recordings when you click "Download"

#### Method 2: URL Parameters

Add recording parameters to any InvoicePlane URL:

```
http://localhost:8080/?record=1&autostart=1&debug=1
```

Parameters:
- `record=1`: Enable the recorder
- `autostart=1`: Start recording automatically
- `debug=1`: Enable debug logging

#### Method 3: Browser Console

Enable recording programmatically:

```javascript
// Enable recording
localStorage.setItem('ip_recording_enabled', 'true');

// Reload the page to activate
location.reload();
```

### Using the Recorder UI

When recording is enabled, you'll see a control panel in the top-right corner:

- **Start**: Begin recording interactions
- **Stop**: Stop the current recording
- **Download**: Save the recording as a JSON file

The panel shows:
- Current recording status
- Number of recorded events
- Session ID for tracking

### Converting to PHPUnit Tests

Convert a recorded JSON file to a PHPUnit test:

```bash
npm run test:convert tests/recordings/invoiceplane-recording-abc123.json
```

Or use the converter directly:

```bash
node tests/playwright/convert-to-phpunit.js tests/recordings/recording.json --output-dir=tests/generated --base-url=http://localhost:8080
```

### Running Playwright Tests

```bash
# Run all Playwright tests
npm test

# Run tests in headed mode (visible browser)
npm run test:headed

# Run a specific test
npx playwright test tests/playwright/example.spec.js
```

### Running Generated PHPUnit Tests

```bash
# Install PHPUnit if not already installed
composer require --dev phpunit/phpunit

# Run a generated test
vendor/bin/phpunit tests/generated/RecordedTestAbc123.php
```

## Advanced Configuration

### Recorder Options

The recorder can be configured with various options:

```javascript
// In browser console or custom script
window.advancedRecorder = new AdvancedRecorder({
    enabled: true,
    debugMode: true,
    autoStart: false,
    maxEvents: 2000,
    excludeSelectors: [
        '.no-record',
        '.tooltip',
        '.popover'
    ],
    includeFormData: true,
    captureScreenshots: false
});
```

### Playwright Configuration

Edit `playwright.config.js` to customize test settings:

```javascript
module.exports = defineConfig({
    // Change base URL
    use: {
        baseURL: 'http://your-invoiceplane-domain.com',
    },
    
    // Add custom browsers
    projects: [
        { name: 'chromium', use: { ...devices['Desktop Chrome'] } },
        { name: 'mobile', use: { ...devices['iPhone 12'] } },
    ],
});
```

### PHPUnit Configuration

Generated tests can be customized by editing the converter:

```javascript
// In convert-to-phpunit.js
const converter = new RecordingToPHPUnitConverter({
    outputDir: 'tests/unit',
    namespace: 'MyApp\\Tests',
    baseUrl: 'https://production.example.com'
});
```

## Best Practices

### Recording

1. **Plan Your Test**: Before recording, plan the user journey you want to test
2. **Use Realistic Data**: Enter realistic test data that represents actual usage
3. **Keep It Focused**: Record one specific feature or workflow per session
4. **Handle Dynamic Content**: Be aware that dynamic IDs or content may affect playback

### Generated Tests

1. **Review Generated Code**: Always review generated tests before using them
2. **Add Assertions**: Enhance generated tests with meaningful assertions
3. **Handle Data Dependencies**: Ensure test data exists or is created during tests
4. **Clean Up**: Add cleanup code to remove test data after tests

### Maintenance

1. **Update Selectors**: When UI changes, update or re-record affected tests
2. **Version Control**: Keep recordings and generated tests in version control
3. **Regular Testing**: Run tests regularly to catch regressions early

## Troubleshooting

### Common Issues

**Recorder not appearing:**
- Check that `?record=1` is in the URL
- Verify the integration was successful
- Check browser console for JavaScript errors

**Elements not found during playback:**
- UI may have changed since recording
- Try re-recording the interaction
- Check if selectors are too specific

**CSRF token errors:**
- Ensure the PHPUnit test properly extracts and uses CSRF tokens
- Check that the test database has the same CSRF configuration

**Tests failing in different environments:**
- Update base URLs in configuration
- Ensure test data exists in the target environment
- Check for environment-specific differences

### Debug Mode

Enable debug mode for detailed logging:

```javascript
// URL parameter
http://localhost:8080/?record=1&debug=1

// Or programmatically
localStorage.setItem('ip_debug', 'true');
```

### Getting Help

1. Check the browser console for error messages
2. Review the generated test code for issues
3. Verify that InvoicePlane is running and accessible
4. Check that all dependencies are installed correctly

## Integration with CI/CD

### GitHub Actions

Add a workflow to run Playwright tests:

```yaml
name: E2E Tests
on: [push, pull_request]
jobs:
  test:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v3
      - uses: actions/setup-node@v3
        with:
          node-version: '18'
      - run: yarn install
      - run: npx playwright install
      - run: npm test
```

### Docker Integration

Run tests in Docker:

```dockerfile
FROM mcr.microsoft.com/playwright:focal
COPY . /app
WORKDIR /app
RUN yarn install
CMD ["npm", "test"]
```

## Contributing

When contributing to the testing system:

1. Follow the existing code style
2. Add tests for new features
3. Update documentation
4. Ensure backwards compatibility

## License

This testing system is part of InvoicePlane and follows the same MIT license.