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
 * @package mediaoembed
 * @subpackage Renderer
 * @version $Id:$
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class Tx_Mediaoembed_OEmbed_Request {

	/**
     * The URL to retrieve embedding information for.
     * This value is required.
     *
     * @var string
     */
	protected $url;

	/**
	 * The maximum width of the embedded resource.
	 * Only applies to some resource types (as specified below).
	 * For supported resource types, this parameter must be respected by providers.
	 * This value is optional.
	 *
	 * @var string
	 */
	protected $maxwidth;

	/**
	 * The maximum height of the embedded resource.
	 * Only applies to some resource types (as specified below).
	 * For supported resource types, this parameter must be respected by providers.
	 * This value is optional.
	 *
	 * @var string
	 */
	protected $maxheight;

	/**
	 * The required response format. When not specified, the provider can return
	 * any valid response format.
	 * When specified, the provider must return data in the request format,
	 * else return an error (see below for error codes). 
	 * This value is optional.
	 *
	 * @var string
	 */
	protected $format;
}
?>