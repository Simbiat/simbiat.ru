/* Generates custom files with MIMEs used by FrankenPHP and HTTP libraries */
const fs = require('fs');
const path = require('path');
const mimeDb = require('mime-db');

// Paths
const projectRoot = path.resolve(__dirname, '../../..');
const customFile = path.resolve(__dirname, 'custom_mime.json');
const outputJson = path.join(projectRoot, '/lib/http20/src/mime.json');
const outputMimeTypes = path.join(projectRoot, 'config/frankenphp/mime.types');

// Load custom MIME data
let customMime = {};
if (fs.existsSync(customFile)) {
  try {
    const customData = fs.readFileSync(customFile, 'utf-8');
    customMime = JSON.parse(customData);
  } catch (err) {
    console.error(`❌ Failed to parse custom.json: ${err.message}`);
    process.exit(1);
  }
}

// Start from mime-db
const extToMime = {};
const mimeToExts = {};

// Helper to safely add MIME and extension
function addMimeExtension(mimeType, ext) {
  if (!mimeToExts[mimeType]) mimeToExts[mimeType] = new Set();
  mimeToExts[mimeType].add(ext);
  if (!extToMime[ext]) extToMime[ext] = mimeType;
}

// Add entries from mime-db
for (const [mimeType, entry] of Object.entries(mimeDb)) {
  if (entry.extensions) {
    for (const ext of entry.extensions) {
      addMimeExtension(mimeType, ext);
    }
  } else {
    if (!mimeToExts[mimeType]) mimeToExts[mimeType] = new Set(); // add empty entry
  }
}

// Merge custom MIME entries
for (const [mimeType, entry] of Object.entries(customMime)) {
  if (!entry || typeof entry !== 'object') {
    console.warn(`⚠️ Skipping "${mimeType}" in custom.json: not an object`);
    continue;
  }

  const extensions = entry.extensions;
  if (!Array.isArray(extensions)) {
    console.warn(`⚠️ Skipping "${mimeType}" in custom.json: extensions field missing or not an array`);
    continue;
  }

  for (const ext of extensions) {
    addMimeExtension(mimeType, ext);
  }
}

// Write JSON: extension → MIME
fs.writeFileSync(outputJson, JSON.stringify(extToMime, null, 2));

// Write mime.types file
const mimeTypesFile = Object.entries(mimeToExts)
                            .map(([mimeType, exts]) => {
                              const extList = [...exts].join(' ');
                              return extList ? `${mimeType} ${extList}` : `${mimeType}`;
                            })
                            .join('\n');

fs.writeFileSync(outputMimeTypes, mimeTypesFile);

console.log('✅ Generated:');
console.log(`- ${path.relative(projectRoot, outputJson)}`);
console.log(`- ${path.relative(projectRoot, outputMimeTypes)}`);
