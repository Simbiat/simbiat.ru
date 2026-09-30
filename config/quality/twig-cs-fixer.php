<?php

/** @noinspection DevelopmentDependenciesUsageInspection */

// Documentation: https://github.com/VincentLanglet/Twig-CS-Fixer/blob/main/docs/configuration.md
// Rules: https://github.com/VincentLanglet/Twig-CS-Fixer/blob/main/docs/rules.md
// A11y rules: https://github.com/PhilDaiguille/twig-a11y-rules

declare(strict_types=1);

use TwigCsFixer\Config\Config;
use TwigCsFixer\Ruleset\Ruleset;
use TwigCsFixer\Standard\Symfony;
use TwigCsFixer\Standard\TwigCsFixer;
use TwigCsFixer\Rules\Node\ForbiddenFilterRule;
use TwigA11y\Standard\A11yStrict;

$ruleset = new Ruleset();

// You can start from a default standard
$ruleset->addStandard(new TwigCsFixer());
$ruleset->addStandard(new Symfony());
$ruleset->addStandard(new A11yStrict());
$ruleset->addRule(new ForbiddenFilterRule(['raw']));

$config = new Config();
$config->allowNonFixableRules();
$config->setRuleset($ruleset);

return $config;
