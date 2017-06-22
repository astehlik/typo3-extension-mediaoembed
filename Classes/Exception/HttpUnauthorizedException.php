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
 * Exception if server returned 401 Unauthorized
 *
 * The specified URL contains a private (non-public) resource.
 * The consumer should provide a link directly to the resource instead
 * of any embedding any extra information, and rely on the provider
 * to provide access control.
 */
class UnauthorizedException extends RequestException
{
    /**
     * Initializes the Exception with a default message and a default code (1303402203).
     *
     * @param string $mediaUrl
     * @param string $requestUrl
     */
    public function __construct($mediaUrl, $requestUrl)
    {
        $message = 'The server returned a 401 Unauthorized error for this URL: %s. ';
        $message .= 'This means that embedding is prohibited for this resource. Please use a direct link instead. ';
        $message .= 'The full request to the server was: %s';
        $message = sprintf($message, $mediaUrl, $requestUrl);
        parent::__construct($message, 1303402203);
    }
}
