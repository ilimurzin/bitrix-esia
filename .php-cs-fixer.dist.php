<?php

declare(strict_types=1);

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__)
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
