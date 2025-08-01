// @ts-check
// eslint.config.mjs
import stylistic from '@stylistic/eslint-plugin'
import globals from 'globals'
import eslint from '@eslint/js'
import ts_eslint from 'typescript-eslint'
import ts_parser from '@typescript-eslint/parser'
import compat from 'eslint-plugin-compat'
import { defineConfig } from 'eslint/config'
import deMorgan from 'eslint-plugin-de-morgan'
import { plugin as ex } from 'eslint-plugin-exception-handling'
import no_unsanitized from 'eslint-plugin-no-unsanitized'
import markdown from '@eslint/markdown'
import json from '@eslint/json'
import no_constructor from 'eslint-plugin-no-constructor-bind'
import redos from 'eslint-plugin-redos'
import pii from 'eslint-plugin-pii'
import xss from 'eslint-plugin-xss'
import const_case from 'eslint-plugin-const-case'
import github from 'eslint-plugin-github'
import plugin_promise from 'eslint-plugin-promise'
import regexp_plugin from 'eslint-plugin-regexp'
import sonarjs from 'eslint-plugin-sonarjs'

// It looks like a false positive for the inspection, so suppressing it.
// noinspection JSCheckFunctionSignatures
export default defineConfig([
  {
    files: ['**/*.js', '**/*.jsx', '**/*.ts', '**/*.tsx'],
    extends: [
      compat.configs['flat/recommended'],
      eslint.configs.recommended,
      stylistic.configs.recommended,
      deMorgan.configs.recommended,
      github.getFlatConfigs().browser,
      github.getFlatConfigs().recommended,
      github.getFlatConfigs().react,
      ...github.getFlatConfigs().typescript,
      plugin_promise.configs['flat/recommended'],
      regexp_plugin.configs['flat/recommended'],
      sonarjs.configs.recommended,
    ],
    plugins: {
      '@stylistic': stylistic,
      'ex': ex,
      'no-unsanitized': no_unsanitized,
      'no-constructor-bind': no_constructor,
      'redos': redos,
      'pii': pii,
      'xss': xss,
      'const-case': const_case,
      'promise': plugin_promise,
    },
    languageOptions: {
      globals: {
        ...globals.browser,
        ...globals.worker,
      },
      ecmaVersion: 'latest',
      sourceType: 'script',
    },
    rules: {
      'compat/compat': 'error',
      'array-callback-return': ['error', {
        checkForEach: true,
      }],
      'no-await-in-loop': 'warn',
      'no-constructor-return': 'warn',
      'no-promise-executor-return': 'warn',
      'no-self-compare': 'error',
      'no-template-curly-in-string': 'error',
      'no-unmodified-loop-condition': 'error',
      'no-unreachable-loop': 'error',
      'require-atomic-updates': 'error',
      'accessor-pairs': ['error', {
        getWithoutSet: true,
      }],
      'arrow-body-style': ['error', 'always'],
      'class-methods-use-this': 'error',
      'max-depth': ['error', 4],
      'max-params': ['error', 5],
      'consistent-return': 'error',
      'curly': 'error',
      'default-case': 'error',
      'default-case-last': 'error',
      'eqeqeq': 'error',
      'func-names': ['error', 'always'],
      'func-style': ['error', 'declaration'],
      'grouped-accessor-pairs': ['error', 'getBeforeSet'],
      'no-bitwise': 'error',
      'no-caller': 'error',
      '@stylistic/no-confusing-arrow': 'warn',
      'no-console': 'warn',
      'no-else-return': 'error',
      'no-eq-null': 'error',
      'no-eval': 'error',
      'no-extend-native': 'error',
      'no-extra-bind': 'error',
      'no-extra-label': 'error',
      'no-implicit-coercion': 'error',
      'no-inline-comments': 'error',
      'no-iterator': 'error',
      'no-label-var': 'error',
      'no-labels': 'error',
      'no-lone-blocks': 'error',
      'no-lonely-if': 'error',
      'no-multi-assign': 'error',
      'no-multi-str': 'error',
      'no-new-func': 'error',
      'no-object-constructor': 'error',
      'no-new-wrappers': 'error',
      'no-octal-escape': 'error',
      'no-plusplus': ['error', {
        allowForLoopAfterthoughts: true,
      }],
      'no-proto': 'error',
      'no-return-assign': 'error',
      'no-script-url': 'error',
      'no-sequences': 'error',
      'no-undef-init': 'error',
      'no-undefined': 'error',
      'no-unneeded-ternary': 'error',
      'no-useless-call': 'error',
      'no-useless-computed-key': 'warn',
      'no-useless-concat': 'warn',
      'no-useless-rename': 'warn',
      'no-var': 'error',
      'object-shorthand': 'error',
      'one-var': ['error', 'never'],
      '@stylistic/one-var-declaration-per-line': ['warn', 'always'],
      'operator-assignment': ['error', 'always'],
      'prefer-arrow-callback': 'error',
      'prefer-const': 'error',
      'prefer-exponentiation-operator': 'error',
      'prefer-numeric-literals': 'error',
      'prefer-object-has-own': 'error',
      'prefer-object-spread': 'error',
      'prefer-promise-reject-errors': 'error',
      'prefer-regex-literals': 'error',
      'prefer-rest-params': 'error',
      'prefer-spread': 'error',
      'prefer-template': 'warn',
      'radix': 'error',
      'require-unicode-regexp': 'error',
      'symbol-description': 'error',
      'yoda': 'error',
      '@stylistic/array-bracket-newline': ['warn', 'consistent'],
      '@stylistic/array-element-newline': 'off',
      '@stylistic/function-call-argument-newline': ['warn', 'consistent'],
      '@stylistic/no-multi-spaces': 'warn',
      '@stylistic/object-property-newline': 'warn',
      '@stylistic/semi-style': ['warn', 'last'],
      '@stylistic/indent': 'warn',
      '@stylistic/quote-props': 'warn',
      '@stylistic/switch-colon-spacing': ['warn', {
        after: true,
        before: false,
      }],
      '@stylistic/wrap-regex': 'warn',
      '@stylistic/brace-style': ['warn', '1tbs'],
      'block-scoped-var': 'error',
      'consistent-this': ['error', 'me'],
      'guard-for-in': 'error',
      'logical-assignment-operators': ['error', 'always'],
      'no-div-regex': 'error',
      'no-implicit-globals': 'error',
      'no-negated-condition': 'error',
      'no-new': 'error',
      'no-nested-ternary': 'error',
      'no-param-reassign': 'error',
      'no-useless-return': 'error',
      'prefer-named-capture-group': 'warn',
      'vars-on-top': 'error',
      '@stylistic/function-paren-newline': ['warn', 'multiline'],
      '@stylistic/newline-per-chained-call': 'warn',
      '@stylistic/spaced-comment': 'warn',
      'no-void': 'error',
      'default-param-last': 'error',
      'no-invalid-this': 'error',
      'no-loop-func': 'error',
      'no-shadow': 'error',
      'no-unused-expressions': 'error',
      'no-use-before-define': 'error',
      'no-array-constructor': 'error',
      'no-empty-function': 'error',
      '@stylistic/no-extra-semi': 'warn',
      '@stylistic/semi': ['warn', 'always'],
      'no-implied-eval': 'error',
      'require-await': 'error',
      'dot-notation': 'error',
      'no-throw-literal': 'error',
      'no-useless-constructor': 'error',
      'ex/no-unhandled': 'error',
      'ex/might-throw': 'warn',
      'ex/use-error-cause': 'error',
      'no-constructor-bind/no-constructor-bind': 'error',
      'no-constructor-bind/no-constructor-state': 'error',
      'redos/no-vulnerable': 'error',
      'no-unsanitized/method': 'error',
      'no-unsanitized/property': 'error',
      'pii/no-email': 'error',
      'pii/no-ip': 'error',
      'pii/no-phone-number': 'error',
      'xss/no-location-href-assign': 'error',
      'const-case/uppercase': 'error',
      'promise/no-multiple-resolved': 'error',
      'promise/prefer-await-to-callbacks': 'warn',
      'promise/prefer-await-to-then': 'warn',
      'promise/prefer-catch': 'error',
      'promise/spec-only': 'error',
      'regexp/no-octal': 'error',
      'regexp/no-standalone-backslash': 'error',
      'regexp/prefer-escape-replacement-dollar-char': 'error',
      'regexp/require-unicode-regexp': 'error',
      'regexp/require-unicode-sets-regexp': 'error',
      'regexp/hexadecimal-escape': 'warn',
      'regexp/letter-case': 'warn',
      'regexp/prefer-lookaround': 'warn',
      'regexp/prefer-named-backreference': 'warn',
      'regexp/prefer-named-capture-group': 'warn',
      'regexp/prefer-named-replacement': 'warn',
      'regexp/prefer-result-array-groups': 'warn',
      'regexp/sort-character-class-elements': 'warn',
      'regexp/unicode-escape': 'error',
      'regexp/unicode-property': 'warn',
      'regexp/grapheme-string-literal': 'warn',
      'sonarjs/arguments-usage': 'error',
      'sonarjs/arrow-function-convention': 'error',
      'sonarjs/aws-iam-all-resources-accessible': 'error',
      'sonarjs/bool-param-default': 'error',
      'sonarjs/class-prototype': 'error',
      'sonarjs/declarations-in-global-scope': 'error',
      'sonarjs/destructuring-assignment-syntax': 'error',
      'sonarjs/elseif-without-else': 'error',
      'sonarjs/file-name-differ-from-class': 'error',
      'sonarjs/max-union-size': 'error',
      'sonarjs/nested-control-flow': 'error',
      'sonarjs/no-built-in-override': 'error',
      'sonarjs/no-collapsible-if': 'error',
      'sonarjs/no-duplicate-string': 'error',
      'sonarjs/no-for-in-iterable': 'error',
      'sonarjs/no-function-declaration-in-block': 'error',
      'sonarjs/no-implicit-dependencies': 'error',
      'sonarjs/no-inconsistent-returns': 'error',
      'sonarjs/no-incorrect-string-concat': 'error',
      'sonarjs/no-nested-incdec': 'error',
      'sonarjs/no-nested-switch': 'error',
      'sonarjs/no-reference-error': 'error',
      'sonarjs/no-require-or-define': 'error',
      'sonarjs/no-return-type-any': 'error',
      'sonarjs/no-undefined-assignment': 'error',
      'sonarjs/no-unused-function-argument': 'error',
      'sonarjs/no-variable-usage-before-declaration': 'error',
      'sonarjs/no-wildcard-import': 'error',
      'sonarjs/non-number-in-arithmetic-expression': 'error',
      'sonarjs/operation-returning-nan': 'error',
      'sonarjs/prefer-immediate-return': 'error',
      'sonarjs/prefer-object-literal': 'error',
      'sonarjs/shorthand-property-grouping': 'error',
      'sonarjs/strings-comparison': 'error',
      'sonarjs/unicode-aware-regex': 'error',
      'sonarjs/values-not-convertible-to-numbers': 'error',
      'sonarjs/function-name': ['warn', {
        format: '^[_a-z][a-zA-Z0-9]+$'
      }],
      'sonarjs/variable-name': ['warn', {
        format: '^[a-z][_a-z]*$',
      }],
      'sonarjs/class-name': ['warn', {
        format: '^[A-Z][a-zA-Z]*$',
      }],
      'github/filenames-match-regex': ['warn', '^[A-Z][a-z0-9_]+[a-zA-Z0-9_]*$'],
      // escompat is bundled with GitHub rules, and some rules do not recognize that options are supported by targeted browsers (a limitation of caniuse-lite)
      'escompat/no-optional-catch': 'off',
      'escompat/no-optional-chaining': 'off',
      // Not sure what installed this, but Stylistic duplicates all the rules, and they seem a bit more readable to me
      'prettier/prettier': 'off',
      // Also not sure where this came from
      'eslint-comments/no-use': 'off',
    },
  },
  {
    files: ['**/*.ts', '**/*.tsx'],
    extends: [
      ts_eslint.configs.recommendedTypeChecked,
      ts_eslint.configs.stylisticTypeChecked,
      ts_eslint.configs.strict,
    ],
    languageOptions: {
      parser: ts_parser,
      parserOptions: {
        tsconfigRootDir: '.',
        project: ['./build/js/Common/tsconfig.json', './build/js/Pages/tsconfig.json'],
      },
    },
    rules: {
      '@typescript-eslint/explicit-function-return-type': 'error',
      '@typescript-eslint/explicit-member-accessibility': 'error',
      '@typescript-eslint/explicit-module-boundary-types': 'error',
      '@typescript-eslint/method-signature-style': ['error', 'method'],
      '@typescript-eslint/no-unnecessary-qualifier': 'error',
      '@typescript-eslint/no-useless-empty-export': 'error',
      '@typescript-eslint/prefer-readonly': 'error',
      '@typescript-eslint/promise-function-async': 'error',
      '@typescript-eslint/require-array-sort-compare': 'error',
      '@typescript-eslint/sort-type-constituents': 'error',
      '@typescript-eslint/strict-boolean-expressions': ['error'],
      '@typescript-eslint/switch-exhaustiveness-check': 'error',
      'no-useless-constructor': 'off',
      '@typescript-eslint/no-useless-constructor': 'error',
      '@typescript-eslint/consistent-type-exports': 'error',
      '@typescript-eslint/consistent-type-imports': 'error',
      '@typescript-eslint/prefer-enum-initializers': 'error',
      '@typescript-eslint/no-confusing-void-expression': 'error',
      'default-param-last': 'off',
      '@typescript-eslint/default-param-last': 'error',
      'no-dupe-class-members': 'off',
      '@typescript-eslint/no-dupe-class-members': 'error',
      'no-invalid-this': 'off',
      '@typescript-eslint/no-invalid-this': 'error',
      'no-loop-func': 'off',
      '@typescript-eslint/no-loop-func': 'error',
      'no-shadow': 'off',
      '@typescript-eslint/no-shadow': 'error',
      'no-use-before-define': 'off',
      '@typescript-eslint/no-use-before-define': 'error',
    },
  },
  {
    files: ['**/*.md', '**/*.markdown'],
    plugins: { markdown },
    extends: ['markdown/recommended'],
    rules: {
      'markdown/no-bare-urls': 'warn',
      'markdown/no-duplicate-headings': 'error',
    },
  },
  {
    files: ['** /*.json'],
    ignores: ['package-lock.json'],
    plugins: { json },
    language: 'json/json',
    ...json.configs.recommended,
  },
  {
    files: ['** /*.jsonc', '.vscode/*.json'],
    plugins: { json },
    language: 'json/jsonc',
    ...json.configs.recommended,
  },
  {
    files: ['** /*.json5'],
    plugins: { json },
    language: 'json/json5',
    ...json.configs.recommended,
  },
])