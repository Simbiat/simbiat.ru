<?php

/** @noinspection DevelopmentDependenciesUsageInspection */

declare(strict_types=1);

use Rector\Carbon\Rector\FuncCall\DateFuncCallToCarbonRector;
use Rector\Carbon\Rector\FuncCall\TimeFuncCallToCarbonRector;
use Rector\Carbon\Rector\New_\DateTimeInstanceToCarbonRector;
use Rector\CodeQuality\Rector\ClassMethod\LocallyCalledStaticMethodToNonStaticRector;
use Rector\CodeQuality\Rector\Identical\FlipTypeControlToUseExclusiveTypeRector;
use Rector\CodeQuality\Rector\If_\ObjectExplicitBoolCompareRector;
use Rector\CodingStyle\Rector\Catch_\CatchExceptionNameMatchingTypeRector;
use Rector\CodingStyle\Rector\ClassMethod\NewlineBeforeNewAssignSetRector;
use Rector\Config\RectorConfig;
use Rector\DeadCode\Rector\ClassMethod\RemoveDuplicatedReturnSelfDocblockRector;
use Rector\DeadCode\Rector\ClassMethod\RemoveMixedDocblockOverruledByNativeTypeRector;
use Rector\DeadCode\Rector\ClassMethod\RemoveUselessParamTagRector;
use Rector\DeadCode\Rector\ClassMethod\RemoveUselessReturnTagRector;
use Rector\DeadCode\Rector\ClassMethod\RemoveUselessUnionReturnDocblockRector;
use Rector\DeadCode\Rector\Expression\RemoveDeadStmtRector;
use Rector\DeadCode\Rector\Property\RemoveUselessVarTagRector;
use Rector\DeadCode\Rector\Stmt\RemoveUnreachableStatementRector;
use Rector\Naming\Rector\Assign\RenameVariableToMatchMethodCallReturnTypeRector;
use Rector\Naming\Rector\Class_\RenamePropertyToMatchTypeRector;
use Rector\Naming\Rector\ClassMethod\RenameParamToMatchTypeRector;
use Rector\Naming\Rector\ClassMethod\RenameVariableToMatchNewTypeRector;
use Rector\Naming\Rector\Foreach_\RenameForeachValueVariableToMatchMethodCallReturnTypeRector;
use Rector\Symfony\Configs\Rector\Closure\FromServicePublicToDefaultsPublicRector;
use Rector\Symfony\Set\SymfonySetList;
use Rector\TypeDeclaration\Rector\BooleanAnd\BinaryOpNullableToInstanceofRector;
use Rector\TypeDeclaration\Rector\Empty_\EmptyOnNullableObjectToInstanceOfRector;
use Rector\TypeDeclarationDocblocks\Rector\ClassMethod\DocblockReturnArrayFromDirectArrayInstanceRector;

/* @noinspection PhpUnhandledExceptionInspection */
return RectorConfig::configure()
                   ->withFileExtensions(['php'])
                   ->withCache('/app/data/temp/rector')
                   ->withPaths([
                       '/app/bin',
                       '/app/config',
                       '/app/packages',
                       '/app/src',
                       '/app/tests',
                   ])
                   ->withSkip([
                       '/app/assets/',
                       '/app/bin/console',
                       '/app/bin/phpunit',
                       '/app/build/',
                       '/app/config/bundles.php',
                       '/app/config/preload.php',
                       '/app/config/reference.php',
                       '/app/data/',
                       '/app/node_modules/',
                       '/app/packages/DDCIcons/icons/',
                       '/app/public/',
                       '/app/templates/',
                       '/app/var/',
                       '/app/vendor/',
                   ])
                   ->withSkip([
                       // Conflicts Symfony.Commenting.FunctionComment.MissingReturn sniff
                       RemoveUselessReturnTagRector::class,
                       // Conflicts EA inspections suggesting null comparison when there are strict types
                       FlipTypeControlToUseExclusiveTypeRector::class,
                       // The below 3 are useful, but suggest using instanceof instead of null comparison (conflicting with EA inspections)
                       BinaryOpNullableToInstanceofRector::class,
                       EmptyOnNullableObjectToInstanceOfRector::class,
                       ObjectExplicitBoolCompareRector::class,
                       // The below 5 use camelCase, which contradicts project's convention of snake_case
                       CatchExceptionNameMatchingTypeRector::class,
                       RenameParamToMatchTypeRector::class,
                       RenameVariableToMatchNewTypeRector::class,
                       RenameForeachValueVariableToMatchMethodCallReturnTypeRector::class,
                       RenamePropertyToMatchTypeRector::class,
                       // Suggests some very ugly names, and often times with unclear logic
                       RenameVariableToMatchMethodCallReturnTypeRector::class,
                       // Removes `(void)` prefix in statements where the result is ignored (which can, at least, generate warnings)
                       RemoveDeadStmtRector::class,
                       // It has some limited use, but replaces `array` with `mixed[]` which conflicts other tools
                       DocblockReturnArrayFromDirectArrayInstanceRector::class,
                       // Based on description and actual suggestions, it looks to be very inconsistent and not particularly useful
                       NewlineBeforeNewAssignSetRector::class,
                       // Can remove placeholders
                       RemoveUnreachableStatementRector::class,
                       // Not using Carbon
                       DateFuncCallToCarbonRector::class,
                       TimeFuncCallToCarbonRector::class,
                       DateTimeInstanceToCarbonRector::class,
                       // These 5 conflict with PHPDoc sniffs
                       RemoveUselessParamTagRector::class,
                       RemoveDuplicatedReturnSelfDocblockRector::class,
                       RemoveUselessUnionReturnDocblockRector::class,
                       RemoveUselessVarTagRector::class,
                       RemoveMixedDocblockOverruledByNativeTypeRector::class,
                       // Essentially suggests to make services public by default, which is bad from security perspective
                       FromServicePublicToDefaultsPublicRector::class,
                       // Conflicts static_private_method rule
                       LocallyCalledStaticMethodToNonStaticRector::class,
                   ])
                   ->withPhpSets()
                   ->withSets([SymfonySetList::CONFIGS])
                   ->withComposerBased(twig: true, doctrine: true, phpunit: true, symfony: true)
                   ->withPreparedSets(
                       deadCode: true,
                       codeQuality: true,
                       codingStyle: true,
                       typeDeclarations: true,
                       typeDeclarationDocblocks: true,
                       privatization: true,
                       naming: true,
                       namedArgs: true,
                       instanceOf: true,
                       if: true,
                       earlyReturn: true,
                       carbon: true,
                       rectorPreset: true,
                       phpunitCodeQuality: true,
                       phpunitNarrowAsserts: true,
                       phpunitMockToStub: true,
                       doctrineCodeQuality: true,
                       symfonyCodeQuality: true,
                       symfonyConfigs: true,
                   )
                   ->withAttributesSets(
                       symfony: true,
                       doctrine: true,
                       phpunit: true,
                   )
                   ->withTreatClassesAsFinal()
                   ->withPHPStanConfigs(['/app/config/quality/phpstan.neon'])
                   ->withSymfonyContainerPhp('/app/var/cache/dev/App_KernelDevDebugContainer.php');
