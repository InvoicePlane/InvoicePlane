#!/usr/bin/env node

/**
 * Generate Package Update Report for Yarn
 * 
 * This script compares the current yarn.lock with the previous version
 * to detect which packages were updated, added, or removed.
 * 
 * It writes a human-readable report to updated-packages.txt.
 */

const fs = require('fs');
const { execSync } = require('child_process');

/**
 * Parse yarn.lock file and extract package versions
 * @param {string} content - yarn.lock file content
 * @returns {Map<string, Set<string>>} - Map of package name to set of versions
 */
function parseYarnLock(content) {
  const packages = new Map();
  const lines = content.split('\n');
  let currentPackage = null;
  let currentVersion = null;

  for (let i = 0; i < lines.length; i++) {
    const line = lines[i];

    // Match package declaration lines (e.g., "package-name@^1.0.0:" or package-name@^1.0.0:)
    // Handle both quoted and unquoted package names
    const quotedMatch = line.match(/^"([^"@]+)@[^"]*":\s*$/);
    const unquotedMatch = !quotedMatch ? line.match(/^([^"@\s]+)@[^\s:]+:\s*$/) : null;
    
    if (quotedMatch) {
      currentPackage = quotedMatch[1];
      currentVersion = null;
      continue;
    } else if (unquotedMatch) {
      currentPackage = unquotedMatch[1];
      currentVersion = null;
      continue;
    }

    // Match version lines (e.g., "  version "1.0.0"")
    const versionMatch = line.match(/^\s+version\s+"([^"]+)"/);
    if (versionMatch && currentPackage) {
      currentVersion = versionMatch[1];
      // Add version to Set for this package
      if (!packages.has(currentPackage)) {
        packages.set(currentPackage, new Set());
      }
      packages.get(currentPackage).add(currentVersion);
      currentPackage = null;
      currentVersion = null;
    }
  }

  return packages;
}

/**
 * Get the previous yarn.lock content from git
 * @returns {string} - Previous yarn.lock content
 */
function getPreviousYarnLock() {
  try {
    return execSync('git show HEAD:yarn.lock', { encoding: 'utf8' });
  } catch (error) {
    console.error('Warning: Could not retrieve previous yarn.lock, assuming empty');
    return '';
  }
}

/**
 * Generate a human-readable report of package changes
 */
function generateReport() {
  try {
    // Read current yarn.lock
    if (!fs.existsSync('yarn.lock')) {
      console.error('Error: yarn.lock not found');
      process.exit(1);
    }

    const currentContent = fs.readFileSync('yarn.lock', 'utf8');
    const previousContent = getPreviousYarnLock();

    // Parse both versions
    const currentPackages = parseYarnLock(currentContent);
    const previousPackages = parseYarnLock(previousContent);

    // Detect changes
    const added = [];
    const updated = [];
    const removed = [];

    // Check for added and updated packages
    for (const [name, currentVersions] of currentPackages) {
      if (!previousPackages.has(name)) {
        // Package is entirely new
        added.push({ name, versions: Array.from(currentVersions).sort() });
      } else {
        // Package exists, check if versions changed
        const previousVersions = previousPackages.get(name);
        
        // Check if the version sets are different (check both directions)
        const hasChanges = currentVersions.size !== previousVersions.size ||
          Array.from(currentVersions).some(v => !previousVersions.has(v)) ||
          Array.from(previousVersions).some(v => !currentVersions.has(v));
        
        if (hasChanges) {
          updated.push({
            name,
            oldVersions: Array.from(previousVersions).sort(),
            newVersions: Array.from(currentVersions).sort()
          });
        }
      }
    }

    // Check for removed packages
    for (const [name, versions] of previousPackages) {
      if (!currentPackages.has(name)) {
        removed.push({ name, versions: Array.from(versions).sort() });
      }
    }

    // Generate report
    let report = '';

    if (added.length === 0 && updated.length === 0 && removed.length === 0) {
      report = 'No package changes detected';
    } else {
      if (updated.length > 0) {
        report += '## Updated Packages\n\n';
        updated.sort((a, b) => a.name.localeCompare(b.name));
        for (const pkg of updated) {
          report += `${pkg.name}: ${pkg.oldVersions.join(', ')} → ${pkg.newVersions.join(', ')}\n`;
        }
      }

      if (added.length > 0) {
        if (report) report += '\n';
        report += '## Added Packages\n\n';
        added.sort((a, b) => a.name.localeCompare(b.name));
        for (const pkg of added) {
          report += `${pkg.name}: (new) → ${pkg.versions.join(', ')}\n`;
        }
      }

      if (removed.length > 0) {
        if (report) report += '\n';
        report += '## Removed Packages\n\n';
        removed.sort((a, b) => a.name.localeCompare(b.name));
        for (const pkg of removed) {
          report += `${pkg.name}: ${pkg.versions.join(', ')} → (removed)\n`;
        }
      }
    }

    // Write to file
    fs.writeFileSync('updated-packages.txt', report, 'utf8');

    // Also print to console for debugging
    console.log('Package update report generated:');
    console.log(report);

    process.exit(0);
  } catch (error) {
    console.error('Error generating package update report:', error.message);
    process.exit(1);
  }
}

// Run the script
generateReport();
