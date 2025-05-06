<?php

declare(strict_types=1);

$finder = (new PhpCsFixer\Finder())
    ->in([
        __DIR__ . '/install',
        __DIR__ . '/lang',
        __DIR__ . '/lib',
        __DIR__ . '/tools',
    ])
    ->append([
        __FILE__,
    ])
;

return (new PhpCsFixer\Config())
    ->setRules([
        '@PER-CS' => true,
    ])
    ->setFinder($finder)
;
