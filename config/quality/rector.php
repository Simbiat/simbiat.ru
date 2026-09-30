<?php

/** @noinspection DevelopmentDependenciesUsageInspection */

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Symfony\Set\SymfonySetList;

/** @noinspection PhpUnhandledExceptionInspection */
return RectorConfig::configure()
                   ->withFileExtensions(['php'])
                   ->withCache('/app/data/temp/rector')
                   ->withPaths([
                       '/app/bin',
                       '/app/config',
                       '/app/packages',
                       '/app/public',
                       '/app/src',
                       '/app/tests'
                   ])
                   ->withSkip([
                       '/app/packages/DDCIcons/icons',
                       '/app/public/assets',
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
