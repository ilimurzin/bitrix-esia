<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/install',
        __DIR__ . '/lang',
        __DIR__ . '/lib',
        __DIR__ . '/tools',
    ])
    ->withPhpSets()
    ->withTypeCoverageLevel(0);
