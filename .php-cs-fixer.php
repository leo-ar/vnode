<?php

// https://symfony.com/doc/current/components/finder.html
$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__)
    ->in('src')
    ->in('spec')
    // ->exclude('somedir')
;

$config = new PhpCsFixer\Config();

return $config->setRules([
    '@PSR12' => true,
])
    ->setFinder($finder);
