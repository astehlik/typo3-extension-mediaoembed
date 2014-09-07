<?php
namespace Sto\Mediaoembed\Content;

/*                                                                        *
 * This script belongs to the TYPO3 extension "mediaoembed".              *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU General Public License as published by the Free   *
 * Software Foundation, either version 3 of the License, or (at your      *
 * option) any later version.                                             *
 *                                                                        *
 * This script is distributed in the hope that it will be useful, but     *
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHAN-    *
 * TABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General      *
 * Public License for more details.                                       *
 *                                                                        *
 * You should have received a copy of the GNU General Public License      *
 * along with the script.                                                 *
 * If not, see http://www.gnu.org/licenses/gpl.html                       *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;

/**
 * Handels TypoScript and FlexForm configuration
 */
class Configuration {

	/**
	 * @var \Sto\Mediaoembed\Utility\ContentObjectUtilities
	 */
	protected $contentObjectUtilities;

	/**
	 * @var \TYPO3\CMS\Extbase\Object\ObjectManagerInterface
	 */
	protected $objectManager;

	/**
	 * Current TypoScript / Flexform configuration
	 *
	 * @var array
	 */
	protected $typoscriptSetup;

	/**
	 * Injects the configuration manager
	 *
	 * @param \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface $configurationManager
	 */
	public function injectConfigurationManager(ConfigurationManagerInterface $configurationManager) {

		$fullTyposcriptSetup = $configurationManager->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT);

		if (!isset($fullTyposcriptSetup['tt_content.']['mediaoembed_oembedmediarenderer.']['20.']['typoscript_settings.'])) {
			throw new \RuntimeException('The configuration at tt_content.mediaoembed_oembedmediarenderer.20.typoscript_settings is missing.');
		}

		$this->typoscriptSetup = $fullTyposcriptSetup['tt_content.']['mediaoembed_oembedmediarenderer.']['20.']['typoscript_settings.'];
	}

	/**
	 * Injects the object manager
	 *
	 * @param \TYPO3\CMS\Extbase\Object\ObjectManagerInterface $objectManager
	 */
	public function injectObjectManager(\TYPO3\CMS\Extbase\Object\ObjectManagerInterface $objectManager) {
		$this->objectManager = $objectManager;
		$this->contentObjectUtilities = $this->objectManager->get('Sto\\Mediaoembed\\Utility\\ContentObjectUtilities');
	}

	/**
	 * The maximum height of the embedded resource.
	 * Only applies to some resource types (as specified below).
	 * For supported resource types, this parameter must be respected by providers.
	 * This value is optional.
	 *
	 * @return int
	 */
	public function getMaxheight() {

		$maxheight = 0;

		if (isset($this->typoscriptSetup['media.'])) {
			$maxheight = $this->contentObjectUtilities->getSingleValueOrContentObject($this->typoscriptSetup['media.'], 'maxheight');
		}

		return intval($maxheight);
	}

	/**
	 * The maximum width of the embedded resource.
	 * Only applies to some resource types (as specified below).
	 * For supported resource types, this parameter must be respected by providers.
	 * This value is optional.
	 *
	 * @return int
	 */
	public function getMaxwidth() {

		$maxwidth = 0;

		if (isset($this->typoscriptSetup['media.'])) {
			$maxwidth = $this->contentObjectUtilities->getSingleValueOrContentObject($this->typoscriptSetup['media.'], 'maxwidth');
		}

		return intval($maxwidth);
	}

	/**
	 * The URL to retrieve embedding information for.
	 * This value is required.
	 *
	 * @return string
	 */
	public function getMediaUrl() {

		$url = '';

		if (isset($this->typoscriptSetup['media.'])) {
			$url = $this->contentObjectUtilities->getSingleValueOrContentObject($this->typoscriptSetup['media.'], 'url');
		}

		return $url;
	}

	/**
	 * TypoScript object for rendering the media item
	 *
	 * @return string
	 */
	public function getRenderItem() {
		return $this->typoscriptSetup['renderItem'];
	}

	/**
	 * TypoScript configuration for rendering the media item
	 *
	 * @return string
	 */
	public function getRenderItemConfig() {
		return $this->typoscriptSetup['renderItem.'];
	}
}