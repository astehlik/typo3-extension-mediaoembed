<?php

$languagePrefix = 'LLL:EXT:mediaoembed/Resources/Private/Language/locallang_db.xlf:';
$languagePrefixColumn = $languagePrefix . 'tx_mediaoembed_provider.';
$showRecordFieldList = 'name,hidden,is_generic,description,url_schemes,endpoint'
    . ',use_generic_providers,embedly_shortname';

return [

    'ctrl' => [
        'label' => 'name',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'delete' => 'deleted',
        'sortby' => 'sorting',
        'prependAtCopy' => 'LLL:EXT:lang/locallang_general.php:LGL.prependAtCopy',
        'adminOnly' => 1,
        'rootLevel' => 1,
        'enablecolumns' => ['disabled' => 'hidden'],
        'title' => $languagePrefix . 'tx_mediaoembed_provider',
        'iconfile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('mediaoembed')
            . 'Resources/Public/Icons/table_provider.png',
        'searchFields' => 'name,description,url_schemes',
    ],

    'interface' => ['showRecordFieldList' => $showRecordFieldList],

    'columns' => [

        'name' => [
            'label' => $languagePrefixColumn . 'name',
            'config' => [
                'type' => 'input',
                'size' => '30',
                'max' => '50',
            ],
        ],

        'hidden' => [
            'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.disable',
            'config' => [
                'type' => 'check',
                'default' => '0',
            ],
        ],

        'is_generic' => [
            'label' => $languagePrefixColumn . 'is_generic',
            'config' => [
                'type' => 'check',
                'default' => '0',
            ],
        ],

        'description' => [
            'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.description',
            'config' => [
                'type' => 'text',
                'rows' => 5,
                'cols' => 30,
            ],
        ],

        'url_schemes' => [
            'label' => $languagePrefixColumn . 'url_schemes',
            'config' => [
                'type' => 'text',
                'rows' => 5,
                'cols' => 30,
            ],
        ],

        'endpoint' => [
            'label' => $languagePrefixColumn . 'endpoint',
            'config' => [
                'type' => 'input',
                'size' => '30',
                'max' => '255',
                'eval' => 'trim',
            ],
        ],

        'use_generic_providers' => [
            'label' => $languagePrefixColumn . 'use_generic_providers',
            'config' => [
                'type' => 'select',
                'size' => '5',
                'maxitems' => '100',
                'foreign_table' => 'tx_mediaoembed_provider',
                'foreign_table_where' => 'AND tx_mediaoembed_provider.is_generic=1'
                    . ' AND tx_mediaoembed_provider.uid!=###THIS_UID###',
            ],
        ],

        'embedly_shortname' => [
            'label' => $languagePrefixColumn . 'embedly_shortname',
            'config' => [
                'type' => 'input',
                'size' => '30',
                'max' => '50',
                'eval' => 'unique',
            ],
        ],
    ],
    'types' => [
        '0' => [
            'showitem' => 'hidden;;;;1-1-1, name;;;;2-2-2, is_generic, description, url_schemes'
                . ', endpoint, use_generic_providers, embedly_shortname',
        ],
    ],
];
