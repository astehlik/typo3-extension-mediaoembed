<?php
namespace Sto\Mediaoembed\Exception;

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
 * Exception if server returned 501 Not Implemented
 *
 * The provider cannot return a response in the requested format.
 * This should be sent when (for example) the request includes format=xml
 * and the provider doesn't support XML responses.
 *
 * However, providers are encouraged to support both JSON and XML.
 */
class HttpNotImplementedException extends RequestException {

	/**
	 * Initializes the Exception with a default message and a default code (1303402211).
	 *
	 * @param string $mediaUrl
	 * @param string $requestFormat
	 * @param string $requestUrl
	 */
	public function __construct($mediaUrl, $requestFormat, $requestUrl) {
		$message = 'The server returned a 501 Not Implemented error for this URL: %s. ';
		$message .= 'Please make sure that the data you trying to contact supports the current request format: %s. ';
		$message .= 'The full request to the server was: %s';
		$message = sprintf($message, $mediaUrl, $requestFormat, $requestUrl);
		parent::__construct($message, 1303402211);
	}
}