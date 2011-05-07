<?php
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
 * @package mediaoembed
 * @subpackage Hooks
 * @version $Id:$
 */
class Tx_Mediaoembed_Hooks_CmsMediaitems {

	/**
	 * This is the name of the render type for which this hook will get active.
	 *
	 * @var string
	 */
	protected static $renderType = 'tx_mediaoembed';

	/**
	 * Addes oEmbed render type to list of render types
	 *
	 * @param array $config The current flexform configuration
	 */
	public function customMediaRenderTypes($config) {

		$config['items'][] = array('oEmbed', self::$renderType, '');

		return $config;
	}

	/**
	 * Renders the embed code that was provided by oEmbed provider
	 *
	 * @param string $renderType
	 * @param array $conf
	 * @param ux_tslib_content_Media $parentContent
	 */
	public function customMediaRender($renderType, $conf, $parentContent) {

		//var_dump(Tx_Extbase_Utility_Extension::createAutoloadRegistryForExtension('mediaoembed', t3lib_extMgm::extPath('mediaoembed')));
		//return;

			// @TODO submit TYPO3 patch that we get the current content
			// that was possibly set by other providers!
		$currentContent = '';

			// we only render if it is our content type
		if ($renderType !== self::$renderType) {
			return $currentContent;
		}

		$content = t3lib_div::makeInstance('Tx_Mediaoembed_Content_Oembed', $parentContent->getCObj());
		$content->injectParentContent($parentContent);
		return $content->render($conf);
	}
}
?>