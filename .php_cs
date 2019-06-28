<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__.'/src')
;

return PhpCsFixer\Config::create()
    ->setRiskyAllowed(true)
    ->setRules([
        '@Symfony' => true,
        'no_superfluous_phpdoc_tags' => true,
        'final_class' => true,
        'array_syntax' => ['syntax' => 'short'],
    ])
    ->setFinder($finder)
;