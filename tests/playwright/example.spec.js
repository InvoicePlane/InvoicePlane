const { test, expect } = require('@playwright/test');

test.describe('InvoicePlane Basic Functionality', () => {
  test.beforeEach(async ({ page }) => {
    // Add the advanced recorder to every page
    await page.addScriptTag({ path: 'assets/core/js/advanced-recording.js' });
    
    // Enable recording for this test
    await page.addInitScript(() => {
      window.IP_RECORDING_ENABLED = true;
    });
  });

  test('homepage loads correctly', async ({ page }) => {
    await page.goto('/');

    // Check if the page title contains InvoicePlane
    await expect(page).toHaveTitle(/InvoicePlane/);
    
    // Check if basic elements are present
    const content = page.locator('#content');
    await expect(content).toBeVisible();
  });

  test('welcome page shows correct buttons', async ({ page }) => {
    await page.goto('/');
    
    // Check for Enter button
    const enterButton = page.locator('a:has-text("Enter")');
    await expect(enterButton).toBeVisible();
    
    // Check for Setup button (if setup is not disabled)
    const setupButton = page.locator('a:has-text("Setup")');
    // Note: This might be hidden if setup is completed
    
    // Check for Get Help button
    const helpButton = page.locator('a:has-text("Get Help")');
    await expect(helpButton).toBeVisible();
    await expect(helpButton).toHaveAttribute('href', 'https://wiki.invoiceplane.com/');
  });

  test('recorder integration works', async ({ page }) => {
    // Navigate with recording enabled
    await page.goto('/?record=1&autostart=1');
    
    // Wait for recorder to initialize
    await page.waitForTimeout(1000);
    
    // Check if recorder is present
    const recorder = await page.evaluate(() => {
      return typeof window.advancedRecorder !== 'undefined';
    });
    
    expect(recorder).toBe(true);
    
    // Check if recorder UI is visible
    const recorderPanel = page.locator('.advanced-recorder');
    await expect(recorderPanel).toBeVisible();
    
    // Check recorder status
    const status = page.locator('#recorder-status');
    await expect(status).toContainText('Recording');
    
    // Perform a test click to generate an event
    const enterButton = page.locator('a:has-text("Enter")');
    if (await enterButton.isVisible()) {
      await enterButton.click();
    }
    
    // Check that events were recorded
    const eventCount = await page.evaluate(() => {
      return window.advancedRecorder ? window.advancedRecorder.events.length : 0;
    });
    
    expect(eventCount).toBeGreaterThan(0);
  });

  test('can download recording', async ({ page }) => {
    await page.goto('/?record=1&autostart=1');
    await page.waitForTimeout(1000);
    
    // Perform some interactions
    const enterButton = page.locator('a:has-text("Enter")');
    if (await enterButton.isVisible()) {
      await enterButton.click();
    }
    
    // Go back
    await page.goBack();
    
    // Stop recording
    await page.click('#recorder-stop');
    
    // Set up download listener
    const downloadPromise = page.waitForEvent('download');
    
    // Click download button
    await page.click('#recorder-download');
    
    // Wait for download
    const download = await downloadPromise;
    expect(download.suggestedFilename()).toMatch(/invoiceplane-recording-.*\.json/);
    
    // Save the download to verify content
    const downloadPath = `tests/recordings/${download.suggestedFilename()}`;
    await download.saveAs(downloadPath);
    
    // Verify the downloaded file contains expected structure
    const fs = require('fs');
    const recordingData = JSON.parse(fs.readFileSync(downloadPath, 'utf8'));
    
    expect(recordingData).toHaveProperty('sessionId');
    expect(recordingData).toHaveProperty('events');
    expect(recordingData.events).toBeInstanceOf(Array);
    expect(recordingData.events.length).toBeGreaterThan(0);
  });
});