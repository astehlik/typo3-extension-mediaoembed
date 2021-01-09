<?php

declare(strict_types=1);

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\VersionNumberUtility;

defined('TYPO3_MODE') or die();

$lllPrefix = 'LLL:' . 'EXT:mediaoembed/Resources/Private/Language/locallang_db.xlf:';
$lllPrefixTtc = 'LLL:' . 'EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:';

$ttContentColumns = [
    'tx_mediaoembed_url' => [
        'label' => $lllPrefix . 'tt_content.tx_mediaoembed_url',
        'config' => [
            'type' => 'input',
            'behaviour' => ['allowLanguageSynchronization' => true],
        ],
    ],
    'tx_mediaoembed_maxwidth' => [
        'label' => $lllPrefix . 'tt_content.tx_mediaoembed_maxwidth',
        'exclude' => true,
        'config' => [
            'type' => 'input',
            'size' => '4',
            'max' => '4',
            'eval' => 'int',
            'range' => [
                'upper' => '999',
                'lower' => '0',
            ],
            'default' => 0,
            'behaviour' => ['allowLanguageSynchronization' => true],
        ],
    ],
    'tx_mediaoembed_maxheight' => [
        'label' => $lllPrefix . 'tt_content.tx_mediaoembed_maxheight',
        'exclude' => true,
        'config' => [
            'type' => 'input',
            'size' => '4',
            'max' => '4',
            'eval' => 'int',
            'range' => [
                'upper' => '999',
                'lower' => '0',
            ],
            'default' => 0,
            'behaviour' => ['allowLanguageSynchronization' => true],
        ],
    ],
    'tx_mediaoembed_play_related' => [
        'label' => $lllPrefix . 'tt_content.tx_mediaoembed_play_related',
        'exclude' => true,
        'config' => [
            'type' => 'check',
            'default' => 1,
        ],
    ],
];

ExtensionManagementUtility::addTCAcolumns('tt_content', $ttContentColumns);

ExtensionManagementUtility::addTcaSelectItem(
    'tt_content',
    'CType',
    [
        $lllPrefix . 'tt_content.CType.I.tx_mediaoembed',
        'mediaoembed_oembedmediarenderer',
        'extensions-mediaoembed-content-externalmedia',
    ],
    'media',
    'after'
);

$GLOBALS['TCA']['tt_content']['ctrl']['typeicon_classes']['mediaoembed_oembedmediarenderer'] =
    'extensions-mediaoembed-content-externalmedia';

$GLOBALS['TCA']['tt_content']['palettes']['tx_mediaoembed_settings'] = [
    'showitem' => 'tx_mediaoembed_url,
    --linebreak--, tx_mediaoembed_maxwidth, tx_mediaoembed_maxheight,
    --linebreak--, tx_mediaoembed_play_related',
    'canNotCollapse' => 1,
];

$GLOBALS['TCA']['tt_content']['types']['mediaoembed_oembedmediarenderer']['showitem'] = '
    --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
        --palette--;' . $lllPrefixTtc . 'palette.general;general,
        --palette--;' . $lllPrefixTtc . 'palette.headers;headers,
        --palette--;' . $lllPrefix . 'palette.tx_mediaoembed_settings;tx_mediaoembed_settings,
    --div--;' . $lllPrefixTtc . 'tabs.appearance,
        --palette--;' . $lllPrefixTtc . 'palette.frames;frames,
        --palette--;' . $lllPrefixTtc . 'palette.appearanceLinks;appearanceLinks,
    --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language,
        --palette--;;language,
    --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
        --palette--;;hidden,
        --palette--;' . $lllPrefixTtc . 'palette.access;access,
    --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:categories,
        categories,
    --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:notes,
        rowDescription,
    --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:extended
';

// Use old structure before TCA was streamlined in 8.5.0 (https://forge.typo3.org/issues/78383)
if (VersionNumberUtility::convertVersionNumberToInteger(TYPO3_version) < 8005000) {
    $GLOBALS['TCA']['tt_content']['types']['mediaoembed_oembedmediarenderer']['showitem'] = '
        --palette--;' . $lllPrefixTtc . 'palette.general;general,
        --palette--;' . $lllPrefixTtc . 'palette.header;header,rowDescription,
        --palette--;' . $lllPrefix . 'palette.tx_mediaoembed_settings;tx_mediaoembed_settings,
    --div--;' . $lllPrefixTtc . 'tabs.appearance,
        --palette--;' . $lllPrefixTtc . 'palette.frames;frames,
    --div--;' . $lllPrefixTtc . 'tabs.access,
        --palette--;' . $lllPrefixTtc . 'palette.visibility;visibility,
        --palette--;' . $lllPrefixTtc . 'palette.access;access,
    --div--;' . $lllPrefixTtc . 'tabs.extended,
    --div--;LLL:EXT:lang/locallang_tca.xlf:sys_category.tabs.category,
        categories
';
}
