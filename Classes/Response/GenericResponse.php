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
 * Responses can specify a resource type, such as photo or video.
 * Each type has specific parameters associated with it.
 * The parameters in this class are valid for all response types.
 *
 * @package mediaoembed
 * @subpackage Response
 * @version $Id:$
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class Tx_Mediaoembed_Response_GenericResponse {

	/**
	 * The name of the author/owner of the resource.
	 * This value is optional.
	 *
	 * @var string
	 */
	protected $author_name;

	/**
	 * A URL for the author/owner of the resource.
	 * This value is optional.
	 *
	 * @var string
	 */
	protected $author_url;

	/**
	 * The suggested cache lifetime for this resource, in seconds.
	 * Consumers may choose to use this value or not.
	 * This value is optional.
	 *
	 * @var string
	 */
	protected $cache_age;

	/**
	 * The name of the resource provider.
	 * This value is optional.
	 *
	 * @var string
	 */
	protected $provider_name;

	/**
	 * The url of the resource provider.
	 * This value is optional.
	 *
	 * @var string
	 */
	protected $provider_url;

	/**
	 * Array containing the json decoded data of the provider's response
	 *
	 * @var array
	 */
	protected $responseDataArray;

	/**
	 * The height of the optional thumbnail.
	 * If this paramater is present, thumbnail_url and thumbnail_width
	 * must also be present.
	 * This value is optional.
	 *
	 * @var string
	 */
	protected $thumbnail_height;

	/**
	 * A URL to a thumbnail image representing the resource.
	 * The thumbnail must respect any maxwidth and maxheight parameters.
	 * If this paramater is present, thumbnail_width and thumbnail_height
	 * must also be present.
	 * Consumers may choose to use this value or not.
	 * This value is optional.
	 *
	 * @var string
	 */
	protected $thumbnail_url;

	/**
	 * The width of the optional thumbnail.
	 * If this paramater is present, thumbnail_url and thumbnail_height
	 * must also be present.
	 * This value is optional.
	 *
	 * @var string
	 */
	protected $thumbnail_width;

	/**
	 * A text title, describing the resource.
	 * This value is optional.
	 *
	 * @var string
	 */
	protected $title;

    /**
     * The resource type. Valid values, along with value-specific parameters, are described below.
     * This value is required.
     *
     * @var string
     */
	protected $type;

	/**
	 * The oEmbed version number. This must be 1.0.
	 * This value is required.
	 *
	 * @var string
	 */
	protected $version;

	/**
	 * Initializes the response parameters that are valid for all
	 * response types.
	 *
	 * @param object the parsed json response
	 */
	public function initializeResponseData($responseData) {
		$this->rawResponseData = $responseData;
		$this->type = $responseData['type'];
		$this->version = $responseData['version'];
		$this->title = $responseData['title'];
		$this->author_name = $responseData['author_name'];
		$this->author_url = $responseData['author_url'];
		$this->provider_name = $responseData['provider_name'];
		$this->provider_url = $responseData['provider_url'];
		$this->cache_age = $responseData['cache_age'];
		$this->thumbnail_url = $responseData['thumbnail_url'];
		$this->thumbnail_width = $responseData['thumbnail_width'];
		$this->thumbnail_height = $responseData['thumbnail_height'];
		$this->initializeTypeSpecificResponseData();
	}

	/**
	 * Initializes the response parameters that are specific for this
	 * resource type.
	 *
	 * @param object the parsed json response
	 */
	public function initializeTypeSpecificResponseData() {
	}

	/**
	 * Getter for the name of the author/owner of the resource.
	 *
	 * @return string
	 */
	public function getAuthorName() {
		return $this->author_name;
	}

	/**
	 * Getter for the URL for the author/owner of the resource.
	 *
	 * @return string
	 */
	public function getAuthorUrl() {
		return $this->author_url;
	}

	/**
	 * Getter for the suggested cache lifetime for this resource, in seconds.
	 *
	 * @return string
	 */
	public function getCacheAge() {
		return $this->cache_age;
	}

	/**
	 * Getter for the name of the resource provider.
	 *
	 * @return string
	 */
	public function getProviderName() {
		return $this->provider_name;
	}

	/**
	 * Getter for the url of the resource provider.
	 *
	 * @return string
	 */
	public function getProviderUrl() {
		return $this->provider_url;
	}

	/**
	 * Getter for the url of the resource provider.
	 *
	 * @return array
	 */
	public function getResponseDataArray() {
		return $this->responseDataArray;
	}

	/**
	 * Getter for the height of the optional thumbnail.
	 *
	 * @return string
	 */
	public function getThumbnailHeight() {
		return $this->thumbnail_height;
	}

	/**
	 * Getter for the URL to a thumbnail image representing the resource.
	 *
	 * @return string
	 */
	public function getThumbnailUrl() {
		return $this->thumbnail_url;
	}

	/**
	 * Getter for the width of the optional thumbnail.
	 *
	 * @return string
	 */
	public function getThumbnailWidth() {
		return $this->thumbnail_width;
	}

	/**
	 * Getter for the text title, describing the resource.
	 *
	 * @return string
	 */
	public function getTitle() {
		return $this->title;
	}

	/**
	 * Getter for the resource type.
	 *
	 * @return string
	 */
	public function getType() {
		return $this->type;
	}

	/**
	 * Getter for the oEmbed version number.
	 *
	 * @return string
	 */
	public function getVersion() {
		return $this->version;
	}
}
?>