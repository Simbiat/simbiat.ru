<?php

/** @noinspection DevelopmentDependenciesUsageInspection, AutoloadingIssuesInspection */

declare(strict_types=1);

use JetBrains\PhpStorm\Pure;
use PhpCsFixer\Config;
use PhpCsFixer\Config\RuleCustomisationPolicyInterface;
use PhpCsFixer\Finder;
use PhpCsFixer\Runner\Parallel\ParallelConfigFactory;

final readonly class SkipSpecificFilesPolicy implements RuleCustomisationPolicyInterface
{
    /**
     * Returns a string that changes when the policy implementation changes in a way that
     * would affect the cache validity.
     *
     * @return non-empty-string
     */
    public function getPolicyVersionForCache(): string
    {
        return hash_file(PHP_VERSION_ID >= 8_01_00 ? 'xxh128' : 'md5', __FILE__);
    }

    /**
     * Customize fixers for given files.
     *
     * @return array
     */
    #[Pure]
    public function getRuleCustomisers(): array
    {
        $skip_for_files = static function (array $paths) {
            return static fn(SplFileInfo $file): bool => !in_array($file->getPathname(), $paths, true);
        };

        return [
            'date_time_immutable' => $skip_for_files([
                '/app/packages/SandClock/SandClock.php',
                '/app/packages/Cron/TaskInstance.php',
            ]),
            'heredoc_to_nowdoc' => $skip_for_files([
                '/app/packages/Sniffs/tests/SQL.php',
            ]),
            'no_alias_language_construct_call' => $skip_for_files([
                '/app/packages/Sniffs/tests/Others.php',
            ]),
            'psr_autoloading' => $skip_for_files([
                '/app/packages/Sniffs/tests/MultiByte.php',
            ]),
        ];
    }
}

return new Config()
    ->setRiskyAllowed(true)
    ->setParallelConfig(ParallelConfigFactory::detect())
    ->setCacheFile('/app/data/temp/.php-cs-fixer.cache')
    // phpcs:ignore SlevomatCodingStandard.Arrays.AlphabeticallySortedByKeys -- There is a logic for order here
    ->setRules([
        '@auto' => true,
        '@auto:risky' => true,
        '@PhpCsFixer' => true,
        '@PhpCsFixer:risky' => true,
        '@DoctrineAnnotation' => true,
        // Overrides SymfonyRisky
        'declare_strict_types' => ['strategy' => 'enforce'],
        // Strict suggests to remove the namespace from some global constants, which conflicts some of the sniffs
        'native_constant_invocation' => ['strict' => false],
        // Extra for PHPUnit
        'php_unit_strict' => true,
        'php_unit_data_provider_name' => true,
        'php_unit_data_provider_return_type' => true,
        // Custom
        'assign_null_coalescing_to_coalesce_equal' => true,
        'attribute_empty_parentheses' => true,
        'combine_consecutive_issets' => true,
        'combine_consecutive_unsets' => true,
        // Custom Prefer Multibyte sniff covers more functions and attempts detecting binary data
        'mb_str_functions' => false,
        'multiline_whitespace_before_semicolons' => ['strategy' => 'no_multi_line'],
        'new_expression_parentheses' => ['use_parentheses' => false],
        'no_redundant_readonly_property' => true,
        'not_operator_with_space' => false,
        'not_operator_with_successor_space' => false,
        'ordered_attributes' => true,
        'ordered_interfaces' => true,
        'ordered_traits' => true,
        'phpdoc_add_missing_param_annotation' => ['only_untyped' => false],
        'phpdoc_line_span' => true,
        'phpdoc_no_duplicate_types' => true,
        'phpdoc_param_order' => true,
        'phpdoc_readonly_class_comment_to_keyword' => true,
        'random_api_migration' => true,
        'return_assignment' => true,
        'return_to_yield_from' => true,
        'self_static_accessor' => true,
        'simplified_if_return' => true,
        'strict_comparison' => true,
        'strict_param' => true,
        'stringable_for_to_string' => true,
        'ternary_to_null_coalescing' => true,
        'use_arrow_functions' => true,
        'yoda_style' => ['equal' => false, 'identical' => false, 'less_and_greater' => false],
        // Tentative, need to observe behavior
        'comment_to_phpdoc' => true,
        'date_time_create_from_format_call' => true,
        'date_time_immutable' => true,
        'explicit_indirect_variable' => true,
        'explicit_string_variable' => true,
        'final_class' => true,
        'final_internal_class' => true,
        'final_public_method_for_abstract_class' => true,
        'group_import' => false,
        'list_syntax' => true,
        // Conflicts other rules related to indentation
        'method_chaining_indentation' => false,
        'multiline_comment_opening_closing' => true,
        'multiline_promoted_properties' => true,
        // Reacts to queries with table prefixes inside suggesting changes, that do not make things more readable, for sure.
        'multiline_string_to_heredoc' => false,
        'no_superfluous_elseif' => true,
        'no_unset_on_property' => true,
        'no_useless_printf' => true,
        'octal_notation' => true,
        'regular_callable_call' => true,
        'simplified_null_return' => true,
        'void_return' => true,
        'yield_from_array_to_yields' => true,
        // This one especially
        'static_private_method' => true,
        // Overrides
        'native_function_invocation' => ['include' => ['@internal', '@compiler_optimized'], 'scope' => 'namespaced', 'strict' => true],
        // PHPStorm @noinspection does not work with `//` comments
        'single_line_comment_style' => ['comment_types' => ['hash']],
        // Need to empty `phpdoc_tags`, because then it conflicts SlevomatCodingStandard.Namespaces.FullyQualifiedClassNameInAnnotation, which is needed, because some tools seem to rely on FQN.
        'fully_qualified_strict_types' => ['leading_backslash_in_global_namespace' => true, 'import_symbols' => true, 'phpdoc_tags' => []],
        // False-positives with some constructs (like phpcs:disable) and with descriptions that are multiple sentences.
        'phpdoc_annotation_without_dot' => false,
        // Conflicts other SAT
        'array_indentation' => false,
        // Conflicts other PHPDocs-related rules
        'no_superfluous_phpdoc_tags' => false,
        // Conflicts SlevomatCodingStandard.Functions.ArrowFunctionDeclaration
        'function_declaration' => false,
        'ordered_class_elements' => ['sort_algorithm' => 'alpha'],
        // This can affect Regex, and I would prefer to avoid accidental changes to those
        'string_implicit_backslashes' => false,
        // Conflicts Symfony's practices with one-liners
        'braces_position' => false,
        'global_namespace_import' => ['import_classes' => false, 'import_constants' => false, 'import_functions' => false],
        // Conflicts other PHPDocs-related rules
        'phpdoc_no_empty_return' => false,
        // If turned on, this affects multi-line declarations (Symfony constructor), which benefit from alignments
        'type_declaration_spaces' => false,
    ])
    ->setRuleCustomisationPolicy(new SkipSpecificFilesPolicy())
    ->setFinder(
        new Finder()
            // root folder to check
            ->in('/app/')
            ->files()
            ->name('*.php')
            ->exclude([
                'assets',
                'build',
                'data',
                'node_modules',
                'packages/DDCIcons/icons',
                'public/assets',
                'var',
                'vendor',
            ])
            ->notPath([
                'bin/console',
                'bin/phpunit',
                'config/bundles.php',
                'config/preload.php',
                'config/reference.php',
                'public/index.php',
            ]),
    );
