<?php

declare(strict_types=1);

defined('TYPO3') || exit;

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    'mediaoembed',
    'Configuration/TypoScript',
    'Media oEmbed'
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    'mediaoembed',
    'Configuration/TypoScript/DefaultProviders',
    'Media oEmbed default providers'
);
