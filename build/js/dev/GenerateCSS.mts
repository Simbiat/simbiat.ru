/**
 * @file Script to bundle CSS files.
 */
// @ts-check
/* Used in a console, so need some output. Used in dev only, so Node.js modules are fine. Paths are predetermined in this file and are technically static. */
/* eslint-disable no-console, import/no-nodejs-modules, security/detect-non-literal-fs-filename */

/* global process */

import fs from 'fs';
import path from 'path';
import postcss from 'postcss';
import atImport from 'postcss-import';
import cssnano from 'cssnano';
import postcss_logical_preset_env from 'postcss-preset-env';

/**
 * Process a CSS file with PostCSS (import resolution and minification).
 * @param input_path - Path to the input CSS file (entry point).
 * @param output_path - Path to the output minified CSS file.
 * @param email - Whether to replace CSS variables.
 */
async function buildCSS(input_path: string, output_path: string, email = false): Promise<void> {
  try {
    const css = fs.readFileSync(input_path, 'utf8');
    let env_config;
    if (email) {
      env_config = {
        stage: 4,
        features: {
          'custom-properties': { preserve: false },
          'logical-properties-and-values': {},
          'logical-viewport-units': { preserve: false },
          'oklab-function': { preserve: false },
        },
      };
    } else {
      env_config = {};
    }
    const result = await postcss([
      atImport(),
      postcss_logical_preset_env(env_config),
      cssnano({ preset: 'default' }),
    ])
      .process(css, {
        from: input_path,
        to: output_path,
        map: {
          inline: false,
          annotation: true,
        },
      });
    fs.mkdirSync(path.dirname(output_path), { recursive: true });
    fs.writeFileSync(output_path, result.css);
    if (typeof result.map === 'object') {
      fs.writeFileSync(`${output_path}.map`, result.map.toString());
    }
    console.log(`✅ Built: ${input_path} → ${output_path}`);
  } catch (err) {
    console.error(`❌ Failed: ${input_path}`, err);
    throw err;
  }
}

/**
 * Wrapper to run all builds and handle exit.
 */
void (async (): Promise<void> => {
  try {
    await Promise.all([
      buildCSS(
        path.resolve('./build/css/app.css'),
        path.resolve('./public/assets/styles/app.css'),
      ),
      buildCSS(
        path.resolve('./build/css/tinymce.css'),
        path.resolve('./public/assets/styles/tinymce.css'),
      ),
      buildCSS(
        path.resolve('./build/css/email.css'),
        path.resolve('./public/assets/styles/email.css'),
        true,
      ),
    ]);
    process.exit(0);
  } catch {
    process.exit(1);
  }
})();
