// noinspection JSUnusedGlobalSymbols
/** @type {import('stylelint').Config} */
export default {
  extends: 'stylelint-config-standard',
  plugins: [
    'stylelint-order',
    'stylelint-no-unsupported-browser-features',
    'stylelint-plugin-logical-css',
    'stylelint-use-nesting',
    'stylelint-high-performance-animation',
    'stylelint-declaration-block-no-ignored-properties',
    'stylelint-no-browser-hacks',
    './build/js/stylelint-plugin-attribute-case-i',
  ],
  rules: {
    'simbiat/require-attribute-i-flag': [true, { severity: 'warning' }],
    'color-no-invalid-hex': true,
    'plugin/no-low-performance-animation-properties': true,
    'plugin/declaration-block-no-ignored-properties': true,
    'plugin/no-browser-hacks': true,
    'font-weight-notation': ['numeric', { severity: 'warning' }],
    'function-url-scheme-allowed-list': ['data', '/^https/'],
    'function-url-no-scheme-relative': true,
    'color-named': ['never', { severity: 'warning' }],
    'unit-no-unknown': true,
    'no-unknown-custom-media': true,
    'function-no-unknown': true,
    // Too many false-positives for no-unknown-custom-properties, since codebase is split
    'no-unknown-custom-properties': false,
    // Similar for animations
    'no-unknown-animations': false,
    'function-linear-gradient-no-nonstandard-direction': true,
    'declaration-no-important': true,
    'custom-property-pattern': [
      '^[a-z_0-9]+$',
      {
        message: 'Expected custom property name to be lowercase snake_case',
        severity: 'warning'
      }
    ],
    'selector-class-pattern': [
      '^[a-z_0-9]+$',
      {
        message: 'Expected class selector name to be lowercase snake_case',
        severity: 'warning'
      }
    ],
    'selector-id-pattern': [
      '^[a-z_0-9]+$',
      {
        message: 'Expected ID selector name to be lowercase snake_case',
        severity: 'warning'
      }
    ],
    'comment-empty-line-before': 'off',
    'comment-whitespace-inside': ['always', { severity: 'warning' }],
    'selector-attribute-quotes': ['always', { severity: 'warning' }],
    'at-rule-empty-line-before': ['always', {
      'ignore': ['first-nested', 'blockless-after-blockless', 'after-comment'],
      severity: 'warning'
    }],
    'custom-property-empty-line-before': ['always', {
      'ignore': ['first-nested', 'after-custom-property', 'after-comment'],
      severity: 'warning'
    }],
    'rule-empty-line-before': ['always', {
      except: ['first-nested', 'after-single-line-comment'],
      ignore: ['after-comment'],
      severity: 'warning'
    }],
    'shorthand-property-no-redundant-values': [true, {
      ignore: ['four-into-three-corner-values'],
      severity: 'warning'
    }],
    'order/properties-alphabetical-order': [true, { severity: 'warning' }],
    'plugin/no-unsupported-browser-features': [true, {
      /* These features are safe to ignore, because browsers will fall back to appropriate values, when necessary. */
      /* This also may include features that are partially supported, but the partial support is acceptable. */
      ignore: [
        'css3-cursors',
        'css3-cursors-newer',
        'intrinsic-width',
        'css-scrollbar',
        'text-size-adjust',
        'mdn-text-decoration-shorthand',
        'css-selection',
        'fullscreen',
        'css-text-indent',
        'css-marker-pseudo',
        'css-unicode-bidi',
        'mdn-css-unicode-bidi-isolate',
        'text-decoration',
        'mdn-text-decoration-line',
        'mdn-text-decoration-color',
        'css-touch-action',
        'css-resize',
        'css-at-counter-style',
        /* This one is actually used to map the fonts to local alternatives */
        'extended-system-fonts',
        /* This looks like a false positive */
        'css-file-selector-button'
      ]
    }],
    'plugin/use-logical-units': [true, { severity: 'warning' }],
    'plugin/use-logical-properties-and-values': [true, { severity: 'warning' }],
    'csstools/use-nesting': 'always',
    // These two require Autoprefixer
    'property-no-vendor-prefix': true,
    'selector-no-vendor-prefix': true,
    // Force oklch() for the sake of tighter control over color matching
    'function-disallowed-list': [
      ['rgb', 'rgba', 'hsl', 'hsla', 'hbw', 'lab', 'lch', 'oklab', 'gray'],
      {
        message: 'Use of oklch() is recommended',
        severity: 'warning'
      }
    ],
    'color-no-hex': [
      true,
      {
        message: 'Use of oklch() is recommended',
        severity: 'warning'
      }
    ],
    'lightness-notation': ['percentage', { severity: 'warning' }],
    'hue-degree-notation': ['angle', { severity: 'warning' }],
    'alpha-value-notation': ['percentage', { severity: 'warning' }],
  },
  overrides: [
    {
      // This folder has files that are meant to override TinyMCE stuff, and they may not conform with project standards as well, so suppressing some rules
      files: ['build/css/tinymce/*.css'],
      rules: {
        'declaration-no-important': false,
        'selector-class-pattern': [{ severity: 'off' }],
        'selector-id-pattern': [{ severity: 'off' }],
      }
    }
  ]
}
