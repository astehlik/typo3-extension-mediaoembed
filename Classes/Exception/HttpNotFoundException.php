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
 * Exception if server returned 404 Not Found
 *
 * The provider has no response for the requested url parameter.
 * This allows providers to be broad in their URL scheme, and then determine
 * at call time if they have a representation to return.
 *
 * @package mediaoembed
 * @subpackage Request
 * @version $Id:$
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class Tx_Mediaoembed_Exception_HttpNotFoundException extends Tx_Mediaoembed_Exception_OEmbedException {
	
	public function __construct($mediaUrl, $requestUrl) {
		$message  = 'The server returned a 404 Not Found error for this URL: %s. ';
		$message .= 'Please make sure that the data you trying to embed still exists. ';
		$message .= 'The full request to the server was: %s';
		$message = sprintf($message, $mediaUrl, $requestUrl);
		parent::__construct($message, 1303401860);
	}
}
?>