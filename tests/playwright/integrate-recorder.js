const fs = require('fs');
const path = require('path');

/**
 * Integration script to add the advanced recorder to InvoicePlane's main scripts.js
 * This allows the recorder to work with the existing application code
 */

class RecorderIntegration {
    constructor() {
        this.scriptsPath = 'assets/core/js/scripts.js';
        this.recorderPath = 'assets/core/js/advanced-recording.js';
        this.backupPath = 'assets/core/js/scripts.js.backup';
    }
    
    /**
     * Integrate the recorder with the main scripts.js file
     */
    integrate() {
        console.log('🔧 Integrating Advanced Recorder with InvoicePlane scripts...');
        
        // Create backup of original scripts.js
        if (!fs.existsSync(this.backupPath)) {
            fs.copyFileSync(this.scriptsPath, this.backupPath);
            console.log('📋 Created backup of scripts.js');
        }
        
        // Read the current scripts.js
        let scriptsContent = fs.readFileSync(this.scriptsPath, 'utf8');
        
        // Check if recorder is already integrated
        if (scriptsContent.includes('AdvancedRecorder')) {
            console.log('✅ Advanced Recorder already integrated');
            return;
        }
        
        // Add recorder initialization code
        const recorderInit = this.generateRecorderInit();
        
        // Find the best place to insert the recorder code
        // We'll add it after the CSRF setup but before the document ready function
        const insertionPoint = scriptsContent.indexOf('$(function () {');
        
        if (insertionPoint !== -1) {
            const beforeDocReady = scriptsContent.substring(0, insertionPoint);
            const afterDocReady = scriptsContent.substring(insertionPoint);
            
            scriptsContent = beforeDocReady + recorderInit + '\n\n' + afterDocReady;
        } else {
            // Fallback: append to the end
            scriptsContent += '\n\n' + recorderInit;
        }
        
        // Write the modified scripts.js
        fs.writeFileSync(this.scriptsPath, scriptsContent);
        
        console.log('✅ Advanced Recorder integrated successfully');
        console.log('💡 To enable recording, add ?record=1 to any URL');
    }
    
    /**
     * Remove the recorder integration
     */
    removeIntegration() {
        console.log('🗑️  Removing Advanced Recorder integration...');
        
        if (fs.existsSync(this.backupPath)) {
            fs.copyFileSync(this.backupPath, this.scriptsPath);
            console.log('✅ Restored original scripts.js from backup');
        } else {
            console.log('⚠️  No backup found, manual cleanup may be required');
        }
    }
    
    /**
     * Generate the recorder initialization code
     */
    generateRecorderInit() {
        return `// Advanced Recorder Integration
// Auto-loads the advanced recorder when recording parameters are present
(function() {
    'use strict';
    
    // Check if recording should be enabled
    const urlParams = new URLSearchParams(window.location.search);
    const enableRecording = urlParams.has('record') || 
                           localStorage.getItem('ip_recording_enabled') === 'true' ||
                           window.IP_RECORDING_ENABLED === true;
    
    if (enableRecording) {
        // Load the advanced recorder script dynamically
        const script = document.createElement('script');
        script.src = '${this.getRelativePath()}advanced-recording.js';
        script.onload = function() {
            console.log('Advanced Recorder loaded');
            
            // Initialize the recorder with InvoicePlane-specific settings
            if (typeof AdvancedRecorder !== 'undefined') {
                window.advancedRecorder = new AdvancedRecorder({
                    enabled: true,
                    debugMode: urlParams.has('debug') || localStorage.getItem('ip_debug') === 'true',
                    autoStart: urlParams.has('autostart'),
                    maxEvents: 2000,
                    excludeSelectors: [
                        '.advanced-recorder',
                        '[data-no-record]',
                        '.select2-container', // Exclude Select2 dropdowns
                        '.datepicker', // Exclude datepicker popups
                        '.tooltip', // Exclude tooltips
                        '.popover' // Exclude popovers
                    ],
                    includeFormData: true,
                    captureScreenshots: false
                });
                
                // Integrate with InvoicePlane's existing AJAX handling
                $(document).ajaxComplete(function(event, xhr, settings) {
                    if (window.advancedRecorder && window.advancedRecorder.recording) {
                        window.advancedRecorder.recordEvent('ajax_complete', {
                            url: settings.url,
                            type: settings.type,
                            status: xhr.status,
                            responseType: xhr.responseType
                        });
                    }
                });
                
                // Integrate with form submissions
                $('form').on('submit', function() {
                    if (window.advancedRecorder && window.advancedRecorder.recording) {
                        const form = this;
                        window.advancedRecorder.recordEvent('form_submit_enhanced', {
                            selector: window.advancedRecorder.generateSelector(form),
                            action: form.action,
                            method: form.method,
                            hasFiles: $(form).find('input[type="file"]').length > 0,
                            csrf_token: $(form).find('input[name="' + csrf_token_name + '"]').val()
                        });
                    }
                });
                
                // Integrate with modal events
                $(document).on('shown.bs.modal', '.modal', function() {
                    if (window.advancedRecorder && window.advancedRecorder.recording) {
                        window.advancedRecorder.recordEvent('modal_shown', {
                            selector: window.advancedRecorder.generateSelector(this),
                            id: this.id,
                            backdrop: $(this).data('backdrop'),
                            keyboard: $(this).data('keyboard')
                        });
                    }
                });
                
                $(document).on('hidden.bs.modal', '.modal', function() {
                    if (window.advancedRecorder && window.advancedRecorder.recording) {
                        window.advancedRecorder.recordEvent('modal_hidden', {
                            selector: window.advancedRecorder.generateSelector(this),
                            id: this.id
                        });
                    }
                });
            }
        };
        
        script.onerror = function() {
            console.warn('Could not load Advanced Recorder script');
        };
        
        document.head.appendChild(script);
    }
})();`;
    }
    
    /**
     * Get the relative path for the recorder script
     */
    getRelativePath() {
        // Assuming scripts.js is in assets/core/js/ and we want to load from the same directory
        return './';
    }
    
    /**
     * Update Gruntfile to include the recorder in the build process
     */
    updateGruntfile() {
        const gruntfilePath = 'Gruntfile.js';
        
        if (!fs.existsSync(gruntfilePath)) {
            console.log('⚠️  Gruntfile.js not found, skipping build integration');
            return;
        }
        
        let gruntContent = fs.readFileSync(gruntfilePath, 'utf8');
        
        // Check if already updated
        if (gruntContent.includes('advanced-recording.js')) {
            console.log('✅ Gruntfile already includes advanced-recording.js');
            return;
        }
        
        // Find the clean task and add our file to exclusions
        const cleanBasicMatch = gruntContent.match(/(clean:\s*{\s*basic:\s*\[)([\s\S]*?)(\])/);
        
        if (cleanBasicMatch) {
            const excludeFiles = cleanBasicMatch[2];
            
            // Add our file to the exclusions
            const newExclusions = excludeFiles.trim().endsWith(',') 
                ? excludeFiles + '\n        "!assets/core/js/advanced-recording.js",'
                : excludeFiles + ',\n        "!assets/core/js/advanced-recording.js"';
            
            gruntContent = gruntContent.replace(cleanBasicMatch[0], 
                cleanBasicMatch[1] + newExclusions + cleanBasicMatch[3]);
        }
        
        // Find the uglify task and add our file
        const uglifyMatch = gruntContent.match(/(uglify:\s*{\s*build:\s*{\s*files:\s*\[)([\s\S]*?)(\])/);
        
        if (uglifyMatch) {
            // The uglify configuration seems to use a different pattern
            // We'll add a comment for manual configuration
            console.log('💡 Note: You may need to manually add advanced-recording.js to the uglify task in Gruntfile.js');
        }
        
        fs.writeFileSync(gruntfilePath, gruntContent);
        console.log('✅ Updated Gruntfile.js to preserve advanced-recording.js');
    }
}

// Command line interface
function main() {
    const args = process.argv.slice(2);
    const command = args[0] || 'integrate';
    
    const integration = new RecorderIntegration();
    
    switch (command) {
        case 'integrate':
            integration.integrate();
            integration.updateGruntfile();
            console.log(`
✅ Integration complete!

Usage:
1. Add ?record=1 to any InvoicePlane URL to enable recording
2. Add &autostart=1 to start recording automatically
3. Add &debug=1 to enable debug mode

Example: http://localhost:8080/?record=1&autostart=1&debug=1

The recorder UI will appear in the top-right corner when enabled.
            `);
            break;
            
        case 'remove':
            integration.removeIntegration();
            break;
            
        default:
            console.log(`
Usage: node integrate-recorder.js [command]

Commands:
  integrate  (default) - Integrate the recorder with InvoicePlane
  remove     - Remove the recorder integration

Examples:
  node integrate-recorder.js
  node integrate-recorder.js integrate
  node integrate-recorder.js remove
            `);
    }
}

if (require.main === module) {
    main();
}

module.exports = { RecorderIntegration };