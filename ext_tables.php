<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

// true => compatible TYPO3 8.7 and upward
if (class_exists('\TYPO3\CMS\Core\Imaging\IconRegistry')) {
    $icons = [
        'content-externalmedia' => 'EXT:mediaoembed/ext_icon.gif',
    ];

    /** @var \TYPO3\CMS\Core\Imaging\IconRegistry $iconRegistry */
    $iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);
    foreach ($icons as $key => $icon) {
        $iconRegistry->registerIcon(
            'extensions-mediaoembed-' . $key,
            \TYPO3\CMS\Core\Imaging\IconProvider\BitmapIconProvider::class,
            [
                'source' => $icon
            ]
        );
    }
    unset($iconRegistry);

} else {

    \TYPO3\CMS\Backend\Sprite\SpriteManager::addSingleIcons(array('content-externalmedia' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath($_EXTKEY) . 'ext_icon.gif'), $_EXTKEY);
}


