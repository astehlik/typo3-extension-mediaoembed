<?php
//declare(ENCODING = 'utf-8');

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
 * Handels TypoScript and FlexForm configuration
 *
 * @package mediaoembed
 * @subpackage Content
 * @version $Id:$
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class Tx_Mediaoembed_Content_Configuration {

	/**
	 * Current TypoScript / Flexform configuration
	 *
	 * @var array
	 */
	protected $conf;

	/**
	 * Constructor for the content configuration.
	 *
	 * @param array $conf Current TypoScript / Flexform configuration
	 */
	public function __construct($conf) {
		$this->conf = $conf;
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
		if (empty($this->conf['height'])) {
			return $this->conf['tx_mediaoembed.']['defaultMaxheight'];
		} else {
			return $this->conf['height'];
		}
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
		if (empty($this->conf['width'])) {
			return $this->conf['tx_mediaoembed.']['defaultMaxwidth'];
		} else {
			return $this->conf['width'];
		}
	}

	/**
     * The URL to retrieve embedding information for.
     * This value is required.
     *
     * @return string
     */
	public function getMediaUrl() {
		return $this->conf['parameter.']['mmFile'];
	}

	/**
	 * TypoScript object for rendering the media item
	 *
	 * @return string
	 */
	public function getRenderItem() {
		return $this->conf['tx_mediaoembed.']['renderItem'];
	}

	/**
	 * TypoScript configuration for rendering the media item
	 *
	 * @return string
	 */
	public function getRenderItemConfig() {
		return $this->conf['tx_mediaoembed.']['renderItem.'];
	}
}