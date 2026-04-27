<?php
declare(strict_types = 1);

/** @noinspection DevelopmentDependenciesUsageInspection */
$finder = new PhpCsFixer\Finder()
    ->in([
        '/app/bin',
        '/app/tests',
        '/app/src',
        '/app/packages',
    ])
    ->exclude([
        '/app/lib/DDCIcons/src/icons',
    ]);

/** @noinspection DevelopmentDependenciesUsageInspection */
return new PhpCsFixer\Config()
    ->setParallelConfig(PhpCsFixer\Runner\Parallel\ParallelConfigFactory::detect())
    ->setCacheFile('/tmp/.php-cs-fixer.cache')
    ->setRules([
        '@PHP8x5Migration' => true,
        '@PHP8x5Migration:risky' => true,
        '@Symfony' => true,
        '@Symfony:risky' => true,
        '@PHPUnit11x0Migration:risky' => true,
        #Overrides SymfonyRisky
        'declare_strict_types' => ['strategy' => 'enforce'],
        'native_constant_invocation' => ['strict' => true],
        #Extra for PHPUnit
        'php_unit_strict' => true,
        'php_unit_data_provider_name' => true,
        'php_unit_data_provider_return_type' => true,
        #Custom
        'assign_null_coalescing_to_coalesce_equal' => true,
        'attribute_empty_parentheses' => true,
        'combine_consecutive_issets' => true,
        'combine_consecutive_unsets' => true,
        'mb_str_functions' => true,
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
        #Tentative, need to observe behavior
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
        'method_chaining_indentation' => true,
        'multiline_comment_opening_closing' => true,
        'multiline_promoted_properties' => true,
        'multiline_string_to_heredoc' => true,
        'no_superfluous_elseif' => true,
        'no_unset_on_property' => true,
        'no_useless_printf' => true,
        'octal_notation' => true,
        'regular_callable_call' => true,
        'simplified_null_return' => true,
        'void_return' => true,
        'yield_from_array_to_yields' => true,
        #This one especially
        'static_private_method' => true,
    ])
    ->setFinder($finder);