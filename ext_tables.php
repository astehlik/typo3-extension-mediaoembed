<?php
defined('TYPO3_MODE') or die();

/** @var \TYPO3\CMS\Core\Imaging\IconRegistry $iconRegistry */
$iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);
$iconRegistry->registerIcon(
    'extensions-mediaoembed-content-externalmedia',
    \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
    ['source' => 'EXT:mediaoembed/Resources/Public/Icons/ContentMedia.svg']
);

unset($iconRegistry);
