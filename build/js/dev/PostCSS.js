// Suppressing no-console, since this is a script supposed to be run outside a browser
/* eslint-disable no-console */
const fs = require('fs');
const path = require('path');
const postcss = require('postcss');
const at_import = require('postcss-import');
const autoprefixer = require('autoprefixer');
const cssnano = require('cssnano');
const postcssPresetEnv = require('postcss-preset-env');
const { postcssVarReplace}= require('postcss-var-replace');
const postcssOKLabFunction = require('@csstools/postcss-oklab-function');
const postcssLogical = require('postcss-logical');
const postcssLogicalViewportUnits = require('@csstools/postcss-logical-viewport-units');

/**
 * Process a CSS file with PostCSS (import resolution + minification)
 * @param {string} input_path - Path to the input CSS file (entry point)
 * @param {string} output_path - Path to the output minified CSS file
 * @param {boolean} email - Whether to replace CSS variables
 */
async function buildCSS(input_path, output_path, email = false) {
  try {
    const css = fs.readFileSync(input_path, 'utf8');
    let result;
    if (email) {
      result = await postcss([
        at_import(),
        postcssVarReplace(),
        autoprefixer(),
        postcssLogical(),
        postcssLogicalViewportUnits({ preserve: false }),
        postcssOKLabFunction({
          preserve: false,
          subFeatures: {
            displayP3: false,
          },
        }),
        postcssPresetEnv({
          stage: 0,
          preserve: false,
        }),
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
    } else {
      result = await postcss([
        at_import(),
        autoprefixer(),
        postcssPresetEnv(),
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
    }
    fs.mkdirSync(path.dirname(output_path), { recursive: true });
    fs.writeFileSync(output_path, result.css);
    if (result.map) {
      fs.writeFileSync(`${output_path}.map`, result.map.toString());
    }
    console.log(`✅ Built: ${input_path} → ${output_path}`);
  } catch (err) {
    console.error(`❌ Failed: ${input_path}`, err);
    throw err;
  }
}

// Wrapper to run all builds and handle exit
(async () => {
  try {
    await buildCSS(
      path.resolve('./build/css/main.css'),
      path.resolve('./public/assets/styles/app.css'),
    );
    await buildCSS(
      path.resolve('./build/css/tinymce.css'),
      path.resolve('./public/assets/styles/tinymce.css'),
    );
    await buildCSS(
      path.resolve('./build/css/email.css'),
      path.resolve('./public/assets/styles/email.css'),
      true,
    );
    process.exit(0);
  } catch {
    process.exit(1);
  }
})();
