import { build } from 'bun';

await build({
  entrypoints: ['./build/js/Init.ts'],
  external: ['*tinymce.min.js'],
  outdir: './public/assets',
  naming: 'app.js',
  minify: true,
  sourcemap: 'linked',
  target: 'browser',
  format: 'esm',
  splitting: false,
});
console.log("✔ Build complete");
