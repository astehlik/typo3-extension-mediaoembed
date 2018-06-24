<?php

//
// Extension Manager/Repository config file for ext "mediaoembed".
//
// Auto generated 16-06-2011 23:48
//
// Manual updates:
// Only the data in the array - everything else is removed by next
// writing. "version" and "dependencies" must not be touched!
//
$EM_CONF[$_EXTKEY] = [
    'title' => 'External media (oEmbed)',
    'description' => 'External media (YouTube, Flickr, ...) content elements using the http://oembed.com/ standard.',
    'category' => 'fe',
    'constraints' => [
        'depends' => [
            'typo3' => '7.6.0-9.4.99',
            'extbase' => '',
        ],
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
    'version' => '1.0.0-dev',
];
