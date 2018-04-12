<?php
defined('TYPO3_MODE') or die();

\TYPO3\CMS\Backend\Sprite\SpriteManager::addSingleIcons(array('content-externalmedia' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath($_EXTKEY) . 'ext_icon.gif'), $_EXTKEY);