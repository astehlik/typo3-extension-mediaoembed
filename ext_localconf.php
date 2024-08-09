<?php

/** @noinspection PhpFullyQualifiedNameUsageInspection */

/** @noinspection PhpMissingStrictTypesDeclarationInspection */

defined('TYPO3') or die();

$bootMediaoembed = function () {
    $lllPrefix = 'LLL:' . 'EXT:mediaoembed/Resources/Private/Language/locallang_db.xlf:';

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        'Mediaoembed',
        'OembedMediaRenderer',
        /** @uses \Sto\Mediaoembed\Controller\OembedController::renderMediaAction() */
        [\Sto\Mediaoembed\Controller\OembedController::class => 'renderMedia'],
        [],
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
    );

    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tce']['formevals'][\Sto\Mediaoembed\Backend\AspectRatioEvaluation::class]
        = '';

    $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry']['tx_mediaoembed_urlinput'] = [
        'nodeName' => 'tx_mediaoembed_urlinput',
        'class' => \Sto\Mediaoembed\Backend\Form\MediaUrlInputElement::class,
        'priority' => 50,
    ];
};

$bootMediaoembed();
unset($bootMediaoembed);
