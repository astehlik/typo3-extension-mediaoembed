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
	 */
	protected $conf;

	/**
	 * Constructor for the content configuration.
	 *
	 * @param $conf Current TypoScript / Flexform configuration
	 */
	public function __construct($conf) {
		$this->conf = $conf;
	}

	public function getMaxheight() {
		return $this->conf['height'];
	}

	public function getMaxwidth() {
		return $this->conf['width'];
	}

	public function getMediaUrl() {
		return $this->conf['parameter.']['mmFile'];
	}

	public function getRenderItem() {
		return $this->conf['tx_mediaoembed.']['renderItem'];
	}

	public function getRenderItemConfig() {
		return $this->conf['tx_mediaoembed.']['renderItem.'];
	}
}