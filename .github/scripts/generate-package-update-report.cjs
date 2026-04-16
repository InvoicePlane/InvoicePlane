#!/usr/bin/env node

/**
 * Generate a readable package update report from yarn.lock changes
 * 
 * This script analyzes the git diff of yarn.lock and generates a tree-like
 * report showing which packages were updated, distinguishing between:
 * - Direct dependencies (from package.json)
 * - Transitive dependencies (dependencies of dependencies)
 */

const fs = require('fs');
const { execSync } = require('child_process');

function parsePackageJson() {
    try {
        const packageJson = JSON.parse(fs.readFileSync('package.json', 'utf8'));
        const directDeps = new Set();
        
        if (packageJson.dependencies) {
            Object.keys(packageJson.dependencies).forEach(dep => directDeps.add(dep));
        }
        if (packageJson.devDependencies) {
            Object.keys(packageJson.devDependencies).forEach(dep => directDeps.add(dep));
        }
        
        return directDeps;
    } catch (error) {
        console.error('Error reading package.json:', error.message);
        return new Set();
    }
}

function parseYarnLockDiff() {
    try {
        // Get the diff of yarn.lock
        const diff = execSync('git diff yarn.lock', { encoding: 'utf8' });
        
        const updates = new Map();
        const lines = diff.split('\n');
        
        let currentPackage = null;
        let oldVersion = null;
        let newVersion = null;
        
        for (let i = 0; i < lines.length; i++) {
            const line = lines[i];
            
            // Detect package name (can be context line or added/removed)
            // Package names can be quoted ("package@version":) or unquoted (package@version:)
            // In git diff, context lines start with space, additions with +, removals with -
            // Match yarn.lock package entry lines: either "package@version": or package@version:
            if (line.match(/^[ +-]([^@\s"]+(@[^:]+)?|"[^"]+"):$/)) {
                // Extract package name - handle both quoted and unquoted
                let packageName;
                
                // Try quoted format first: "packagename@version":
                let match = line.match(/^[ +-]"([^"@]+)(?:@[^"]+)?":$/);
                if (match) {
                    packageName = match[1];
                } else {
                    // Try unquoted format: packagename@version:
                    match = line.match(/^[ +-]([^@\s]+)(?:@[^:]+)?:$/);
                    if (match) {
                        packageName = match[1];
                    }
                }
                
                if (packageName) {
                    // Only reset if we're seeing a new package
                    if (currentPackage !== packageName) {
                        currentPackage = packageName;
                        // Reset version tracking when we see a new package
                        oldVersion = null;
                        newVersion = null;
                    }
                }
            }
            
            // Detect version changes
            if (line.match(/^[-+]  version "/)) {
                const versionMatch = line.match(/^([-+])  version "([^"]+)"/);
                if (versionMatch) {
                    const changeType = versionMatch[1];
                    const version = versionMatch[2];
                    
                    if (changeType === '-') {
                        oldVersion = version;
                    } else if (changeType === '+') {
                        newVersion = version;
                    }
                    
                    // If we have both versions, record the update
                    if (oldVersion && newVersion && oldVersion !== newVersion && currentPackage) {
                        // Basic version format validation (allows semver and other common formats)
                        const versionRegex = /^[\d.]+[-+a-zA-Z0-9.]*$/;
                        if (versionRegex.test(oldVersion) && versionRegex.test(newVersion)) {
                            // Only record if not already recorded or if this is a different version pair
                            const existingUpdate = updates.get(currentPackage);
                            if (!existingUpdate || (existingUpdate.from !== oldVersion || existingUpdate.to !== newVersion)) {
                                updates.set(currentPackage, { from: oldVersion, to: newVersion });
                            }
                        }
                        // Reset version tracking but keep currentPackage for potential additional entries
                        oldVersion = null;
                        newVersion = null;
                    }
                }
            }
        }
        
        return updates;
    } catch (error) {
        console.error('Error parsing yarn.lock diff:', error.message);
        return new Map();
    }
}

function generateReport() {
    const directDeps = parsePackageJson();
    const updates = parseYarnLockDiff();
    
    if (updates.size === 0) {
        return 'No package updates detected.';
    }
    
    let report = '';
    
    // Separate direct and transitive dependencies
    const directUpdates = [];
    const transitiveUpdates = [];
    
    for (const [pkg, versions] of updates.entries()) {
        const updateInfo = { pkg, ...versions };
        
        if (directDeps.has(pkg)) {
            directUpdates.push(updateInfo);
        } else {
            transitiveUpdates.push(updateInfo);
        }
    }
    
    // Sort alphabetically
    directUpdates.sort((a, b) => a.pkg.localeCompare(b.pkg));
    transitiveUpdates.sort((a, b) => a.pkg.localeCompare(b.pkg));
    
    // Generate report header
    report += '╔═══════════════════════════════════════════════════════════════╗\n';
    report += '║                    Package Update Report                     ║\n';
    report += '╚═══════════════════════════════════════════════════════════════╝\n\n';
    
    // Direct dependencies section
    if (directUpdates.length > 0) {
        report += '📦 DIRECT DEPENDENCIES (from package.json)\n';
        report += '─'.repeat(65) + '\n\n';
        
        for (const { pkg, from, to } of directUpdates) {
            report += `  ✓ ${pkg}\n`;
            report += `    ${from} → ${to}\n\n`;
        }
    } else {
        report += '📦 DIRECT DEPENDENCIES (from package.json)\n';
        report += '─'.repeat(65) + '\n';
        report += '  No direct dependencies updated.\n\n';
    }
    
    // Transitive dependencies section
    if (transitiveUpdates.length > 0) {
        report += '\n🔗 TRANSITIVE DEPENDENCIES (dependencies of dependencies)\n';
        report += '─'.repeat(65) + '\n\n';
        
        for (const { pkg, from, to } of transitiveUpdates) {
            report += `  └─ ${pkg}\n`;
            report += `     ${from} → ${to}\n\n`;
        }
    }
    
    // Summary
    report += '\n' + '═'.repeat(65) + '\n';
    report += `SUMMARY: ${directUpdates.length} direct, ${transitiveUpdates.length} transitive (${updates.size} total)\n`;
    report += '═'.repeat(65) + '\n';
    
    return report;
}

// Main execution
try {
    const report = generateReport();
    
    // Write to file
    fs.writeFileSync('updated-packages.txt', report);
    console.log('✓ Report saved to updated-packages.txt');
} catch (error) {
    console.error('Fatal error:', error.message);
    process.exit(1);
}
