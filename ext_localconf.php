<?php

/** @noinspection PhpFullyQualifiedNameUsageInspection */

/** @noinspection PhpMissingStrictTypesDeclarationInspection */

defined('TYPO3_MODE') or die();

$bootMediaoembed = function () {
    $currentVersion = \TYPO3\CMS\Core\Utility\VersionNumberUtility::getNumericTypo3Version();
    $lllPrefix = 'LLL:' . 'EXT:mediaoembed/Resources/Private/Language/locallang_db.xlf:';

    $registerPluginLegacy = function () {
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
            'Sto.Mediaoembed',
            'OembedMediaRenderer',
            /** @uses \Sto\Mediaoembed\Controller\OembedController::renderMediaAction() */
            ['Oembed' => 'renderMedia'],
            [],
            \TYPO3\CMS\Extbase\Utility\ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
        );
    };

    $registerPlugin = function () {
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
            'Mediaoembed',
            'OembedMediaRenderer',
            /** @uses \Sto\Mediaoembed\Controller\OembedController::renderMediaAction() */
            [\Sto\Mediaoembed\Controller\OembedController::class => 'renderMedia'],
            [],
            \TYPO3\CMS\Extbase\Utility\ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
        );
    };

    $usesNewPluginRegistration = version_compare($currentVersion, '10.0.0', '>=');
    $pluginRegistrationMethod = $usesNewPluginRegistration ? $registerPlugin : $registerPluginLegacy;
    $pluginRegistrationMethod();

    $hasNewUpgradeWizard = version_compare($currentVersion, '9.4.0', '>=');
    $upgradeWizardClass = $hasNewUpgradeWizard
        ? \Sto\Mediaoembed\Install\MigrateContentElementsUpdate::class
        : \Sto\Mediaoembed\Install\MigrateContentElementsUpdateLegacy::class;

    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/install']['update']['tx_mediaoembed_migratecontentelements'] =
        $upgradeWizardClass;

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
        '
mod.wizards.newContentElement {
	wizardItems {
		special.elements {
			mediaoembed_oembedmediarenderer {
				iconIdentifier = extensions-mediaoembed-content-externalmedia
				title = ' . $lllPrefix . 'tt_content.CType.I.tx_mediaoembed
				description = ' . $lllPrefix . 'new_content_element_wizard_oembedmediarenderer_description
				tt_content_defValues {
					CType = mediaoembed_oembedmediarenderer
				}
			}
		}
		special.show := addToList(mediaoembed_oembedmediarenderer)
	}
}
'
    );

    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tce']['formevals'][\Sto\Mediaoembed\Backend\AspectRatioEvaluation::class]
        = '';

    $dispatcher = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\SignalSlot\Dispatcher::class);
    /** @uses \Sto\Mediaoembed\Backend\EditDocumentControllerHooks::addUrlParserJs() */
    $dispatcher->connect(
        \TYPO3\CMS\Backend\Controller\EditDocumentController::class,
        'initAfter',
        \Sto\Mediaoembed\Backend\EditDocumentControllerHooks::class,
        'addUrlParserJs'
    );
};

$bootMediaoembed();
unset($bootMediaoembed);
