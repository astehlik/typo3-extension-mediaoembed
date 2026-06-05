<?php

/** @noinspection PhpFullyQualifiedNameUsageInspection */

/** @noinspection PhpMissingStrictTypesDeclarationInspection */

defined('TYPO3') or die();

$bootMediaoembed = function () {
    // @extensionScannerIgnoreLine - False positive
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        'Mediaoembed',
        'OembedMediaRenderer',
        /** @uses \Sto\Mediaoembed\Controller\OembedController::renderMediaAction() */
        [\Sto\Mediaoembed\Controller\OembedController::class => 'renderMedia'],
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
