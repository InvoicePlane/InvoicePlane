const { chromium } = require('playwright');
const fs = require('fs');
const path = require('path');

/**
 * Interactive recorder for InvoicePlane using Playwright
 * Starts a browser session with the advanced recorder enabled
 * Allows manual interaction recording for test generation
 */

async function startRecordingSession() {
    console.log('🎬 Starting InvoicePlane Recording Session...');
    
    const browser = await chromium.launch({
        headless: false, // Must be headed for manual interaction
        slowMo: 50, // Slow down operations for better visibility
        args: ['--start-maximized']
    });
    
    const context = await browser.newContext({
        viewport: { width: 1920, height: 1080 },
        recordVideo: {
            dir: 'tests/recordings/',
            size: { width: 1920, height: 1080 }
        }
    });
    
    const page = await context.newPage();
    
    // Add the advanced recorder script to every page
    await context.addInitScript({ path: 'assets/core/js/advanced-recording.js' });
    
    // Enable recording mode
    await context.addInitScript(() => {
        window.IP_RECORDING_ENABLED = true;
    });
    
    // Navigate to InvoicePlane (assumes running on localhost:8080)
    const baseUrl = process.env.IP_BASE_URL || 'http://localhost:8080';
    
    try {
        console.log(`📍 Navigating to ${baseUrl}...`);
        await page.goto(baseUrl + '?record=1&autostart=1');
        
        // Wait for the page to load and recorder to initialize
        await page.waitForTimeout(2000);
        
        // Check if recorder is loaded
        const recorderExists = await page.evaluate(() => {
            return typeof window.advancedRecorder !== 'undefined';
        });
        
        if (recorderExists) {
            console.log('✅ Advanced Recorder loaded successfully');
        } else {
            console.log('⚠️  Advanced Recorder not found, injecting manually...');
            await page.addScriptTag({ path: 'assets/core/js/advanced-recording.js' });
            
            // Initialize recorder manually
            await page.evaluate(() => {
                if (typeof AdvancedRecorder !== 'undefined') {
                    window.advancedRecorder = new AdvancedRecorder({
                        enabled: true,
                        debugMode: true,
                        autoStart: true
                    });
                }
            });
        }
        
        console.log(`
🎯 Recording Session Started!

Instructions:
1. The browser window should be open with InvoicePlane
2. You should see a recording panel in the top-right corner
3. If recording didn't start automatically, click the "Start" button
4. Perform your test actions (login, create invoice, etc.)
5. Click "Stop" when finished
6. Click "Download" to save the recording
7. Press Ctrl+C in this terminal to close the browser

The session will remain open until you close it manually.
Recorded data will be saved for conversion to PHPUnit tests.
        `);
        
        // Set up listeners for recording events
        page.on('console', msg => {
            if (msg.text().includes('[AdvancedRecorder]')) {
                console.log('🎬', msg.text());
            }
        });
        
        // Set up download listener
        page.on('download', async download => {
            const fileName = download.suggestedFilename();
            if (fileName.includes('recording')) {
                const savePath = path.join('tests/recordings', fileName);
                await download.saveAs(savePath);
                console.log(`💾 Recording saved to: ${savePath}`);
            }
        });
        
        // Wait for user to finish (or Ctrl+C)
        await new Promise((resolve) => {
            process.on('SIGINT', () => {
                console.log('\n🛑 Recording session interrupted by user');
                resolve();
            });
            
            // Also wait for page close
            page.on('close', () => {
                console.log('📄 Page closed');
                resolve();
            });
        });
        
    } catch (error) {
        console.error('❌ Error during recording session:', error);
    } finally {
        console.log('🧹 Cleaning up...');
        await browser.close();
    }
}

// Handle command line arguments
const args = process.argv.slice(2);
const options = {
    url: args.find(arg => arg.startsWith('--url='))?.split('=')[1] || 'http://localhost:8080',
    output: args.find(arg => arg.startsWith('--output='))?.split('=')[1] || 'tests/recordings'
};

// Create recordings directory if it doesn't exist
if (!fs.existsSync(options.output)) {
    fs.mkdirSync(options.output, { recursive: true });
}

// Start the recording session
if (require.main === module) {
    startRecordingSession()
        .then(() => {
            console.log('✅ Recording session completed');
            process.exit(0);
        })
        .catch(error => {
            console.error('❌ Recording session failed:', error);
            process.exit(1);
        });
}

module.exports = { startRecordingSession };