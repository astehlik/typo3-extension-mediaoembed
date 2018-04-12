<?php
defined('TYPO3_MODE') or die();

/** @var \TYPO3\CMS\Core\Imaging\IconRegistry $iconRegistry */
$iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);
$iconRegistry->registerIcon(
    'extensions-mediaoembed-content-externalmedia',
    \TYPO3\CMS\Core\Imaging\IconProvider\BitmapIconProvider::class,
    ['source' => 'EXT:mediaoembed/ext_icon.gif',]
);

unset($iconRegistry);
