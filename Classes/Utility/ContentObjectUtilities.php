<?php
namespace Sto\Mediaoembed\Utility;

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

/**
 * Utility class for simplifying handling of TypoScript object
 * rendering
 */
class ContentObjectUtilities implements \TYPO3\CMS\Core\SingletonInterface {

	/**
	 * @var \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer
	 */
	protected $contentObject;

	/**
	 * Injector for the configuration manager
	 *
	 * @param \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface $configurationManager
	 */
	public function injectConfigurationManager(\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface $configurationManager) {
		$this->contentObject = $configurationManager->getContentObject();
	}

	/**
	 * If the array key exists in the given configuration and a
	 * configuration exists for the given array key ('key.') than the
	 * configuration will be rendered as a TypoScript object.
	 *
	 * If the no configuration is available the content of the given key
	 * will be returned.
	 *
	 * If the key is missing completely an empty string is returned
	 *
	 * @param array $configuration The TypoScript configuration
	 * @param string $key The configuration key
	 * @return string The rendered content object or a string
	 */
	public function getSingleValueOrContentObject($configuration, $key) {

		if (!array_key_exists($key, $configuration)) {
			return '';
		}

		if (!array_key_exists($key . '.', $configuration)) {
			return $configuration[$key];
		}

		return $this->contentObject->cObjGetSingle($configuration[$key], $configuration[$key . '.']);
	}
}