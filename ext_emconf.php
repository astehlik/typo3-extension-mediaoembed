<?php

/** @noinspection PhpMissingStrictTypesDeclarationInspection */

/** @var string $_EXTKEY */
// phpcs:ignore Squiz.NamingConventions.ValidVariableName
$EM_CONF[$_EXTKEY] = [
    'title' => 'External media (oEmbed)',
    'description' => 'External media (YouTube, Flickr, ...) content elements using the http://oembed.com/ standard.',
    'category' => 'fe',
    'constraints' => [
        'depends' => ['typo3' => '12.4.0-12.4.99'],
        'conflicts' => [],
        'suggests' => [],
    ],
    'loadOrder' => '',
    'state' => 'stable',
    'uploadfolder' => 0,
    'createDirs' => '',
    'clearCacheOnLoad' => 1,
    'author' => 'Alexander Stehlik',
    'author_email' => 'alexander.stehlik.deleteme@gmail.com',
    'author_company' => '',
    'version' => '12.0.0',
];
