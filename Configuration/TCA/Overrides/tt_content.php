<?php
defined('TYPO3_MODE') or die();

$lllPrefix = 'LLL:' . 'EXT:mediaoembed/Resources/Private/Language/locallang_db.xlf:';

$ttContentColumns = [
    'tx_mediaoembed_url' => [
        'label' => $lllPrefix . 'tt_content.tx_mediaoembed_url',
        'config' => ['type' => 'input'],
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
                'lower' => '25',
            ],
            'default' => 0,
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
                'lower' => '25',
            ],
            'default' => 0,
        ],
    ],
];

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('tt_content', $ttContentColumns, true);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTcaSelectItem(
    'tt_content',
    'CType',
    [
        $lllPrefix . 'tt_content.CType.I.tx_mediaoembed',
        'mediaoembed_oembedmediarenderer',
        'i/tt_content_mm.gif',
    ],
    'media',
    'after'
);

$GLOBALS['TCA']['tt_content']['ctrl']['typeicon_classes']['mediaoembed_oembedmediarenderer'] =
    'extensions-mediaoembed-content-externalmedia';

$GLOBALS['TCA']['tt_content']['palettes']['tx_mediaoembed_settings'] = [
    'showitem' => 'tx_mediaoembed_url, --linebreak--, tx_mediaoembed_maxwidth, tx_mediaoembed_maxheight',
    'canNotCollapse' => 1,
];

$GLOBALS['TCA']['tt_content']['types']['mediaoembed_oembedmediarenderer']['showitem'] = '
		--palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.general;general,
		--palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.header;header,
		--palette--;' . $lllPrefix . 'palette.tx_mediaoembed_settings;tx_mediaoembed_settings,
	--div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.appearance,
		--palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.frames;frames,
	--div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.access,
		--palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.visibility;visibility,
		--palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.access;access,
	--div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.extended';
