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
 * Responses can specify a resource type, such as photo or video.
 * Each type has specific parameters associated with it.
 * The parameters in this class are valid for all response types.
 * 
 * @package mediaoembed
 * @subpackage Response
 * @version $Id:$
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
abstract class Tx_Mediaoembed_Response_AbstractResponse {

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
	 * A text title, describing the resource.
	 * This value is optional.
	 *
	 * @var string
	 */
	protected $title;

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
	 * The name of the resource provider.
	 * This value is optional.
	 *
	 * @var string
	 */
	protected $provider_name;

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
	 * The suggested cache lifetime for this resource, in seconds.
	 * Consumers may choose to use this value or not.
	 * This value is optional.
	 *
	 * @var string
	 */
	protected $cache_age;
	
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
	 * The height of the optional thumbnail.
	 * If this paramater is present, thumbnail_url and thumbnail_width
	 * must also be present.
	 * This value is optional.
	 *
	 * @var string
	 */
	protected $thumbnail_height;
	
}
?>