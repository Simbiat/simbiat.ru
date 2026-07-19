/**
 * @file Generates custom files with MIMEs used by FrankenPHP and HTTP libraries.
 */

/* Used in a console, so need some output. Used in dev only, so Node.js modules are fine. Paths are predetermined in this file and are technically static. */
/* eslint-disable no-console, import/no-nodejs-modules */

/* global process */

import fs from 'fs';
import path from 'path';
import mime_db from 'mime-db';

/** Shape of a single mime-db entry. */
interface MimeEntry {
  extensions?: string[]
}

(function generateMIMEFiles(): void {
  // Paths
  const dir_name = import.meta.dirname;
  const project_root = path.resolve(dir_name, '../../..');
  const custom_file = path.resolve(dir_name, 'custom_mime.json');
  const output_json = path.join(project_root, '/packages/http20/mime.json');
  const output_mime_types = path.join(project_root, 'build/docker/frankenphp/mime.types');

  // Load custom MIME data
  let custom_mime: Record<string, MimeEntry> = {};
  if (fs.existsSync(custom_file)) {
    try {
      const custom_data = fs.readFileSync(custom_file, 'utf-8');
      custom_mime = JSON.parse(custom_data) as Record<string, MimeEntry>;
    } catch (err) {
      console.error(`❌ Failed to parse custom.json: ${err instanceof Error ? err.message : String(err)}`);
      process.exit(1);
    }
  }

  // Start from mime-db
  const extension_to_mime: Record<string, string> = {};
  const mime_to_extension: Record<string, Set<string>> = {};

  // Add entries from mime-db
  const typed_mime_db = mime_db as Record<string, MimeEntry>;
  for (const [mime_type, entry] of Object.entries(typed_mime_db)) {
    if (Array.isArray(entry.extensions)) {
      for (const ext of entry.extensions) {
        // eslint-disable-next-line security/detect-object-injection
        mime_to_extension[mime_type] ??= new Set<string>();
        // eslint-disable-next-line security/detect-object-injection
        mime_to_extension[mime_type].add(ext);
        // eslint-disable-next-line security/detect-object-injection
        extension_to_mime[ext] ??= mime_type;
      }
    } else {
      // add empty entry
      // eslint-disable-next-line security/detect-object-injection
      mime_to_extension[mime_type] ??= new Set<string>();
    }
  }

  // Merge custom MIME entries
  for (const [mime_type, entry] of Object.entries(custom_mime)) {
    if (typeof entry !== 'object') {
      console.warn(`⚠️ Skipping "${mime_type}" in custom.json: not an object`);
      continue;
    }

    const { extensions } = entry;
    if (!Array.isArray(extensions)) {
      console.warn(`⚠️ Skipping "${mime_type}" in custom.json: extensions field missing or not an array`);
      continue;
    }

    for (const ext of extensions) {
      // eslint-disable-next-line security/detect-object-injection
      mime_to_extension[mime_type] ??= new Set<string>();
      // eslint-disable-next-line security/detect-object-injection
      mime_to_extension[mime_type].add(ext);
      // eslint-disable-next-line security/detect-object-injection
      extension_to_mime[ext] ??= mime_type;
    }
  }

  // Write JSON: extension → MIME
  fs.writeFileSync(output_json, JSON.stringify(extension_to_mime, null, 2));

  // Write the mime.types file
  const mime_types_file = Object.entries(mime_to_extension)
                                .map(([mime_type, extensions]) => {
                                  const extension_list = [...extensions].join(' ');
                                  return extension_list ? `${mime_type} ${extension_list}` : `${mime_type}`;
                                })
                                .join('\n');
  fs.writeFileSync(output_mime_types, mime_types_file);

  console.log('✅ Generated:');
  console.log(`- ${path.relative(project_root, output_json)}`);
  console.log(`- ${path.relative(project_root, output_mime_types)}`);
})();
