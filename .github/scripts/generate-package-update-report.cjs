#!/usr/bin/env node

/**
 * Generate a readable report of updated packages from yarn.lock changes
 */

const fs = require('fs');
const { execSync } = require('child_process');

try {
    // Get the diff of yarn.lock
    const diff = execSync('git diff yarn.lock', { encoding: 'utf-8' });
    
    if (!diff) {
        fs.writeFileSync('updated-packages.txt', 'No packages were updated.');
        process.exit(0);
    }

    // Parse the diff to find package changes
    const lines = diff.split('\n');
    const updatedPackages = new Set();
    
    for (let i = 0; i < lines.length; i++) {
        const line = lines[i];
        
        // Look for package name lines (they typically start with package names after +/-)
        if (line.match(/^[-+]"?[@a-zA-Z0-9_-]+/)) {
            // Extract package name
            const match = line.match(/^[-+]"?(@?[a-zA-Z0-9_-]+(?:\/[a-zA-Z0-9_-]+)?)/);
            if (match && match[1]) {
                updatedPackages.add(match[1]);
            }
        }
    }

    // Generate the report
    let report = '';
    if (updatedPackages.size > 0) {
        report = 'Updated packages:\n\n';
        report += Array.from(updatedPackages).sort().map(pkg => `- ${pkg}`).join('\n');
    } else {
        report = 'Yarn dependencies were updated, but specific package changes could not be determined from diff.';
    }

    fs.writeFileSync('updated-packages.txt', report);
    console.log('Package update report generated successfully');
    process.exit(0);

} catch (error) {
    console.error('Error generating package update report:', error.message);
    fs.writeFileSync('updated-packages.txt', 'Error generating package update report.');
    process.exit(1);
}
