<?php

declare(strict_types=1);

use Sto\Mediaoembed\Backend\AspectRatioEvaluation;
use Sto\Mediaoembed\Content\Configuration;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

defined('TYPO3') || exit;

$lllPrefix = 'LLL:EXT:mediaoembed/Resources/Private/Language/locallang_db.xlf:';
$lllPrefixTtc = 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:';

$ttContentColumns = [
    'tx_mediaoembed_url' => [
        'label' => $lllPrefix . 'tt_content.tx_mediaoembed_url',
        'config' => [
            'type' => 'input',
            'renderType' => 'tx_mediaoembed_urlinput',
            'behaviour' => ['allowLanguageSynchronization' => true],
        ],
    ],
    'tx_mediaoembed_aspect_ratio' => [
        'label' => $lllPrefix . 'tt_content.tx_mediaoembed_aspect_ratio',
        'config' => [
            'type' => 'input',
            'behaviour' => ['allowLanguageSynchronization' => true],
            'placeholder' => Configuration::ASPECT_RATIO_DEFAULT,
            'size' => '4',
            'eval' => AspectRatioEvaluation::class,
        ],
    ],
    'tx_mediaoembed_maxwidth' => [
        'label' => $lllPrefix . 'tt_content.tx_mediaoembed_maxwidth',
        'exclude' => true,
        'config' => [
            'type' => 'number',
            'format' => 'integer',
            'size' => '4',
            'range' => ['lower' => '0'],
            'default' => 0,
            'behaviour' => ['allowLanguageSynchronization' => true],
        ],
    ],
    'tx_mediaoembed_maxheight' => [
        'label' => $lllPrefix . 'tt_content.tx_mediaoembed_maxheight',
        'exclude' => true,
        'config' => [
            'type' => 'number',
            'format' => 'integer',
            'size' => '4',
            'range' => ['lower' => '0'],
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
        'label' => $lllPrefix . 'tt_content.CType.I.tx_mediaoembed',
        'description' => $lllPrefix . 'new_content_element_wizard_oembedmediarenderer_description',
        'value' => 'mediaoembed_oembedmediarenderer',
        'icon' => 'extensions-mediaoembed-content-externalmedia',
        'group' => 'special',
    ],
    'media',
    'after',
);

$GLOBALS['TCA']['tt_content']['ctrl']['typeicon_classes']['mediaoembed_oembedmediarenderer'] =
    'extensions-mediaoembed-content-externalmedia';

$GLOBALS['TCA']['tt_content']['palettes']['tx_mediaoembed_settings'] = [
    'showitem' => 'tx_mediaoembed_url,
    --linebreak--, tx_mediaoembed_maxwidth, tx_mediaoembed_maxheight,
    --linebreak--, tx_mediaoembed_play_related,
    --linebreak--, tx_mediaoembed_aspect_ratio',
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
