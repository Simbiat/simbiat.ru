/**
 * @file Compile TypeScript into a singular JavaScript file.
 */
import { build } from 'bun';

await build({
  entrypoints: ['./build/js/Init.mts'],
  external: ['*tinymce.min.js'],
  outdir: './public/assets',
  naming: 'app.js',
  minify: {
    whitespace: true,
    syntax: true,
    identifiers: true,
  },
  sourcemap: 'linked',
  target: 'browser',
  format: 'esm',
  splitting: false,
});
// Used in console, so need some output
// eslint-disable-next-line no-console
console.log('✅ Build complete');
