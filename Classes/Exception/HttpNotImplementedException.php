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
 * Exception if server returned 501 Not Implemented
 *
 * The provider cannot return a response in the requested format.
 * This should be sent when (for example) the request includes format=xml
 * and the provider doesn't support XML responses.
 *
 * However, providers are encouraged to support both JSON and XML.
 *
 * @package mediaoembed
 * @subpackage Request
 * @version $Id:$
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class Tx_Mediaoembed_Exception_HttpNotImplementedException extends Tx_Mediaoembed_Exception_RequestException {

	public function __construct($mediaUrl, $requestFormat, $requestUrl) {
		$message  = 'The server returned a 501 Not Implemented error for this URL: %s. ';
		$message .= 'Please make sure that the data you trying to contact supports the current request format: %s. ';
		$message .= 'The full request to the server was: %s';
		$message = sprintf($message, $mediaUrl, $requestFormat, $requestUrl);
		parent::__construct($message, 1303402211);
	}
}
?>