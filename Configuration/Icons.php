<?php

declare(strict_types=1);

use TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider;

return [
    'extensions-mediaoembed-content-externalmedia' => [
        'provider' => SvgIconProvider::class,
        ['source' => 'EXT:mediaoembed/Resources/Public/Icons/ContentMedia.svg'],
    ],
];
