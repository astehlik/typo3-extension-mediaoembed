<?php
declare(ENCODING = 'utf-8');

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
 * 
 * This type is used for representing playable videos.
 * Responses of this type must obey the maxwidth and maxheight request parameters.
 * If a provider wishes the consumer to just provide a thumbnail, rather than an
 * embeddable player, they should instead return a photo response type.
 * 
 * @package mediaoembed
 * @subpackage Response
 * @version $Id:$
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class Tx_Mediaoembed_Response_VideoResponse extends Tx_Mediaoembed_Response_AbstractResponse {

	/**
     * The HTML required to embed a video player.
     * The HTML should have no padding or margins.
     * Consumers may wish to load the HTML in an off-domain iframe to avoid
     * XSS vulnerabilities.
     * This value is required.
     *
     * @var string
     */
	protected $html;

	/**
	 * The width in pixels required to display the HTML.
	 * This value is required.
	 *
	 * @var string
	 */
	protected $width;

	/**
	 * The height in pixels required to display the HTML.
	 * This value is required.
	 *
	 * @var string
	 */
	protected $height;
}
?>