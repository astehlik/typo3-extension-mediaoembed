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
 * Exception if server returned 404 Not Found
 *
 * The provider has no response for the requested url parameter.
 * This allows providers to be broad in their URL scheme, and then determine
 * at call time if they have a representation to return.
 */
class HttpNotFoundException extends OEmbedException
{
    /**
     * Initializes the Exception with a default message and a default code (1303401860).
     *
     * @param string $mediaUrl
     * @param string $requestUrl
     */
    public function __construct($mediaUrl, $requestUrl)
    {
        $message = 'The server returned a 404 Not Found error for this URL: %s. ';
        $message .= 'Please make sure that the data you trying to embed still exists. ';
        $message .= 'The full request to the server was: %s';
        $message = sprintf($message, $mediaUrl, $requestUrl);
        parent::__construct($message, 1303401860);
    }
}
