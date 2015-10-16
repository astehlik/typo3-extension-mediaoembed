<?php
namespace Sto\Mediaoembed\Response;

/*                                                                        *
 * This script belongs to the TYPO3 Extension "mediaoembed".              *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU General Public License, either version 3 of the   *
 * License, or (at your option) any later version.                        *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

/**
 * This type is used for representing playable videos.
 * Responses of this type must obey the maxwidth and maxheight request parameters.
 * If a provider wishes the consumer to just provide a thumbnail, rather than an
 * embeddable player, they should instead return a photo response type.
 */
class VideoResponse extends GenericResponse {

	/**
	 * The height in pixels required to display the HTML.
	 * This value is required.
	 *
	 * @var string
	 */
	protected $height;

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
	 * Initializes the response parameters that are specific for this
	 * resource type.
	 */
	public function initializeTypeSpecificResponseData() {
		$this->html = $this->responseDataArray['html'];
		$this->width = $this->responseDataArray['width'];
		$this->height = $this->responseDataArray['height'];
	}

	/**
	 * Getter for the height in pixels required to display the HTML.
	 *
	 * @return string
	 */
	public function getHeight() {
		return $this->height;
	}

	/**
	 * Getter for the HTML required to embed a video player.
	 *
	 * @return string
	 */
	public function getHtml() {
		return $this->html;
	}

	/**
	 * Getter for the width in pixels required to display the HTML.
	 *
	 * @return string
	 */
	public function getWidth() {
		return $this->width;
	}
}