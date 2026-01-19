#!/usr/bin/env node

/**
 * Generate Package Update Report
 * 
 * Parses yarn.lock to detect updated packages and writes a human-readable report
 * to updated-packages.txt for use in GitHub Actions workflows.
 * 
 * This script compares the old and new yarn.lock files to identify:
 * - New packages
 * - Updated packages (version changes)
 * - Removed packages
 * 
 * Output is formatted as a markdown-compatible report with sections for
 * direct dependencies (from package.json) and transient dependencies.
 */

const fs = require('fs');
const { execSync } = require('child_process');

/**
 * Parse a yarn.lock file and extract package versions
 * @param {string} content - The content of the yarn.lock file
 * @returns {Map<string, string>} Map of package names to versions
 */
function parseYarnLock(content) {
  const packages = new Map();
  const lines = content.split('\n');
  let currentPackage = null;
  let currentVersion = null;

  for (const line of lines) {
    // Match package declaration lines (e.g., "package-name@^1.0.0:")
    const packageMatch = line.match(/^"?([^@\s]+)@[^"]*"?:/);
    if (packageMatch) {
      currentPackage = packageMatch[1];
      currentVersion = null;
      continue;
    }

    // Match version lines (e.g., "  version \"1.0.0\"")
    const versionMatch = line.match(/^\s+version\s+"([^"]+)"/);
    if (versionMatch && currentPackage) {
      currentVersion = versionMatch[1];
      packages.set(currentPackage, currentVersion);
      currentPackage = null;
    }
  }

  return packages;
}

/**
 * Load and parse package.json to get direct dependencies
 * @returns {Set<string>} Set of direct dependency names
 */
function getDirectDependencies() {
  try {
    const packageJson = JSON.parse(fs.readFileSync('package.json', 'utf8'));
    const dependencies = new Set();
    
    if (packageJson.dependencies) {
      Object.keys(packageJson.dependencies).forEach(dep => dependencies.add(dep));
    }
    if (packageJson.devDependencies) {
      Object.keys(packageJson.devDependencies).forEach(dep => dependencies.add(dep));
    }
    
    return dependencies;
  } catch (error) {
    console.error('Error reading package.json:', error.message);
    return new Set();
  }
}

/**
 * Main function to generate the package update report
 */
function generateReport() {
  try {
    // Get the old yarn.lock from git
    let oldLockContent;
    try {
      oldLockContent = execSync('git show HEAD:yarn.lock', { encoding: 'utf8' });
    } catch (error) {
      // If we can't get the old yarn.lock, create an empty one
      oldLockContent = '';
    }

    // Read the new yarn.lock
    let newLockContent;
    try {
      newLockContent = fs.readFileSync('yarn.lock', 'utf8');
    } catch (error) {
      console.error('Error: yarn.lock not found');
      process.exit(1);
    }

    // Parse both lock files
    const oldPackages = parseYarnLock(oldLockContent);
    const newPackages = parseYarnLock(newLockContent);
    
    // Get direct dependencies from package.json
    const directDeps = getDirectDependencies();

    // Track changes
    const directChanges = [];
    const transientChanges = [];

    // Check for new and updated packages
    for (const [name, newVersion] of newPackages) {
      const isDirect = directDeps.has(name);
      
      if (!oldPackages.has(name)) {
        // New package
        const change = `${name}: (new) → ${newVersion}`;
        if (isDirect) {
          directChanges.push(change);
        } else {
          transientChanges.push(change);
        }
      } else if (oldPackages.get(name) !== newVersion) {
        // Updated package
        const oldVersion = oldPackages.get(name);
        const change = `${name}: ${oldVersion} → ${newVersion}`;
        if (isDirect) {
          directChanges.push(change);
        } else {
          transientChanges.push(change);
        }
      }
    }

    // Check for removed packages
    for (const [name, version] of oldPackages) {
      if (!newPackages.has(name)) {
        const isDirect = directDeps.has(name);
        const change = `${name}: ${version} → (removed)`;
        if (isDirect) {
          directChanges.push(change);
        } else {
          transientChanges.push(change);
        }
      }
    }

    // Generate output
    let output = '';

    if (directChanges.length === 0 && transientChanges.length === 0) {
      output = 'No package changes detected\n';
    } else {
      if (directChanges.length > 0) {
        output += '## Direct Dependencies (from package.json)\n\n';
        directChanges.forEach(change => {
          output += `${change}\n`;
        });
      }

      if (transientChanges.length > 0) {
        if (directChanges.length > 0) {
          output += '\n';
        }
        output += '## Transient Dependencies (indirect)\n\n';
        transientChanges.forEach(change => {
          output += `${change}\n`;
        });
      }
    }

    // Write to file
    fs.writeFileSync('updated-packages.txt', output);
    console.log('Package update report generated successfully');
    process.exit(0);

  } catch (error) {
    console.error('Error generating report:', error.message);
    console.error(error.stack);
    process.exit(1);
  }
}

// Run the script
generateReport();
