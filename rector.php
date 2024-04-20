<?php

/**
 * Rector - Instant Upgrades and Automated Refactoring
 * Rector instantly upgrades and refactors the PHP code of your application.
 * see: https://github.com/rectorphp/rector
 *
 * call like this:  php vendor/bin/rector process  --clear-cache --dry-run
 */

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Doctrine\Set\DoctrineSetList;
use Rector\Set\ValueObject\LevelSetList;
use Rector\ValueObject\PhpVersion;


return static function (RectorConfig $rectorConfig): void {

    $rectorConfig->phpVersion(PhpVersion::PHP_80);

    $rectorConfig->sets([
        LevelSetList::UP_TO_PHP_81,

        DoctrineSetList::ANNOTATIONS_TO_ATTRIBUTES,
    ]);

    $rectorConfig->paths([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ]);

    $rectorConfig->skip([
    ]);

    $rectorConfig->parallel(300);
};
