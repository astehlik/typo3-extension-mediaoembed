<?php

declare(strict_types=1);

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

defined('TYPO3') || exit;

ExtensionManagementUtility::addStaticFile(
    'mediaoembed',
    'Configuration/TypoScript',
    'Media oEmbed',
);

ExtensionManagementUtility::addStaticFile(
    'mediaoembed',
    'Configuration/TypoScript/DefaultProviders',
    'Media oEmbed default providers',
);
