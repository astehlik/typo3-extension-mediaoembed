<?php
declare(strict_types=1);

namespace Sto\Mediaoembed\ViewHelpers\Link;

/*                                                                        *
 * This script belongs to the TYPO3 Extension "mediaoembed".              *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU General Public License, either version 3 of the   *
 * License, or (at your option) any later version.                        *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * View helper that accepts a TypoLink parameter
 */
class TypolinkViewHelper extends AbstractViewHelper
{
    /**
     * @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface
     * */
    protected $configurationManager;

    public function injectConfigurationManager(ConfigurationManagerInterface $configurationManager)
    {
        $this->configurationManager = $configurationManager;
    }

    /**
     * Renders a TypoLink
     *
     * @param string $parameter
     * @param array $aTagParams
     * @param array $configOverride
     * @return string
     */
    public function render($parameter, array $aTagParams = null, array $configOverride = [])
    {
        $contentObject = $this->configurationManager->getContentObject();

        $config = ['parameter' => $parameter];

        if (isset($aTagParams)) {
            $config['ATagParams'] = \TYPO3\CMS\Core\Utility\GeneralUtility::implodeAttributes($aTagParams);
        }

        \TYPO3\CMS\Core\Utility\ArrayUtility::mergeRecursiveWithOverrule($config, $configOverride);

        $config = $this->getExternalTarget($config);

        return $contentObject->typoLink($this->renderChildren(), $config);
    }

    /**
     * Uses the extTarget config from lib.parseFunc.makelinks.http.extTarget
     * to automatically build the extTarget config for the current typolink.
     *
     * @param array $config
     * @return array
     */
    protected function getExternalTarget(array $config)
    {
        // If a value was already set we disable the automatic detection.
        if (isset($config['extTarget.']['override'])) {
            return $config;
        }

        $tsfe = $this->getTyposcriptFrontendController();

        // If no target is configured we do not need to modify the configuration.
        if (empty($tsfe->tmpl->setup['lib.']['parseFunc.']['makelinks.']['http.']['extTarget.']['override'])) {
            return $config;
        }

        if (!isset($config['extTarget'])) {
            $config['extTarget'] = '';
        }

        $config['extTarget.']['override'] =
            $tsfe->tmpl->setup['lib.']['parseFunc.']['makelinks.']['http.']['extTarget.']['override'];

        return $config;
    }

    /**
     * @return \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController
     */
    protected function getTyposcriptFrontendController()
    {
        return $GLOBALS['TSFE'];
    }
}
