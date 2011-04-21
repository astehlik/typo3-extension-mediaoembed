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
 * Exception if server returned 401 Unauthorized
 *
 * The specified URL contains a private (non-public) resource.
 * The consumer should provide a link directly to the resource instead
 * of any embedding any extra information, and rely on the provider
 * to provide access control.
 *
 * @package mediaoembed
 * @subpackage Request
 * @version $Id:$
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class Tx_Mediaoembed_Exception_UnauthorizedException extends Tx_Mediaoembed_Exception_OEmbedException {
	
public function __construct($mediaUrl, $requestUrl) {
		$message  = 'The server returned a 401 Unauthorized error for this URL: %s. ';
		$message .= 'This means that embedding is prohibited for this resource. Please use a direct link instead. ';
		$message .= 'The full request to the server was: %s';
		$message = sprintf($message, $mediaUrl, $requestUrl);
		parent::__construct($message, 1303402203);
	}
}
?>