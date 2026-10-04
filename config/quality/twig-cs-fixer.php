<?php

/** @noinspection DevelopmentDependenciesUsageInspection */

// Documentation: https://github.com/VincentLanglet/Twig-CS-Fixer/blob/main/docs/configuration.md
// Rules: https://github.com/VincentLanglet/Twig-CS-Fixer/blob/main/docs/rules.md
// A11y rules: https://github.com/PhilDaiguille/twig-a11y-rules

declare(strict_types=1);

use TwigCsFixer\Config\Config;
use TwigCsFixer\File\Finder;
use TwigCsFixer\Ruleset\Ruleset;
use TwigCsFixer\Standard\Symfony;
use TwigCsFixer\Standard\TwigCsFixer;
use TwigCsFixer\Rules\Node\ForbiddenFilterRule;
use TwigA11y\Standard\A11yStrict;

return new Config()
    ->allowNonFixableRules()
    ->setCacheFile('/app/data/temp/.twig-cs-fixer.cache')
    ->setRuleset(
        new Ruleset()
            ->addStandard(new TwigCsFixer())
            ->addStandard(new Symfony())
            ->addStandard(new A11yStrict())
            ->addRule(new ForbiddenFilterRule(['raw'])),
    )
    ->setFinder(
        new Finder()
            ->in('/app/templates')
            ->files()
            ->name('*.twig'),
    );
