<?php

/** @noinspection DevelopmentDependenciesUsageInspection */

declare(strict_types=1);

use ShipMonk\ComposerDependencyAnalyser\Config\Configuration;

return new Configuration()
    //// Adjusting scanned paths
    ->addPathToScan('/app/bin', isDev: false)
    ->addPathToScan('/app/config', isDev: false)
    ->addPathToScan('/app/config/composer-dependency-analyser.php', isDev: true)
    ->addPathToScan('/app/config/php-cs-fixer.php', isDev: true)
    ->addPathToScan('/app/config/rector.php', isDev: true)
    ->addPathToScan('/app/config/twig-cs-fixer.php', isDev: true)
    ->addPathToScan('/app/packages', isDev: false)
    ->addPathToScan('/app/public', isDev: false)
    ->addPathToScan('/app/src', isDev: false)
    ->setFileExtensions(['php']) // applies only to directory scanning, not directly listed files

    //// Adjust analysis
    ->enableAnalysisOfUnusedDevDependencies() // dev packages are often used only in CI, so this is not enabled by default
    ->disableReportingUnmatchedIgnores(); // do not report ignores that never matched any error

// TODO: Enable once there is a public update with this feature
//// Make CLI file paths clickable to your IDE (uses OSC 8 hyperlinks)
// Available placeholders: {file}, {relFile}, {line}
// ->setEditorUrl('phpstorm://open?file={file}&line={line}');
