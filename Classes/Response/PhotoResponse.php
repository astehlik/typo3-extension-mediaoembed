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
 * This type is used for representing static photos.
 * Responses of this type must obey the maxwidth and maxheight request parameters.
 *
 * @package mediaoembed
 * @subpackage Response
 * @version $Id:$
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class Tx_Mediaoembed_Response_PhotoResponse extends Tx_Mediaoembed_Response_GenericResponse {
	/**
	 * The height in pixels of the image specified in the url parameter.
	 * This value is required.
	 *
	 * @var string
	 */
	protected $height;

	/**
	 * The width in pixels of the image specified in the url parameter.
	 * This value is required.
	 *
	 * @var string
	 */
	protected $width;

	/**
     * The source URL of the image.
     * Consumers should be able to insert this URL into an <img> element.
     * Only HTTP and HTTPS URLs are valid.
     * This value is required.
     *
     * @var string
     */
	protected $url;

	/**
	 * Initializes the response parameters that are specific for this
	 * resource type.
	 *
	 * @param object the parsed json response
	 */
	public function initializeTypeSpecificResponseData() {
		$this->url = $this->responseDataArray['url'];
		$this->width = $this->responseDataArray['width'];
		$this->height = $this->responseDataArray['height'];
	}

	/**
	 * Getter for the height in pixels of the image specified in the url parameter.
	 *
	 * @return string
	 */
	public function getHeight() {
		return $this->height;
	}

	/**
	 * Getter for the source URL of the image.
	 *
	 * @return string
	 */
	public function getUrl() {
		return $this->url;
	}

	/**
	 * Getter for the width in pixels of the image specified in the url parameter.
	 *
	 * @return string
	 */
	public function getWidth() {
		return $this->width;
	}
}
?>