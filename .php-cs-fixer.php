<?php

$finder = (new PhpCsFixer\Finder())
    ->exclude('.github')
    ->exclude('bin')
    ->exclude('cache')
    ->exclude('config')
    ->exclude('migrations')
    ->exclude('tests-reports')
    ->exclude('var')
    ->exclude('vendor')
    ->in(__DIR__)
;

return (new PhpCsFixer\Config())
    ->setRules([
        '@PSR12' => true,
    ])
    ->setFinder($finder)
;
