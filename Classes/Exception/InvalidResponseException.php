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
 * This Exception will be thrown when the server returned a response that could not be parsed.
 */
class InvalidResponseException extends RequestException
{
    /**
     * Initializes the Exception with a default message and a default code (1303402784).
     *
     * @param string $response
     */
    public function __construct($response)
    {
        $message = 'The server returned an invalid response that could not be parsed. The servers response was: %s';
        $message = sprintf($message, htmlspecialchars($response));
        parent::__construct($message, 1303402784);
    }
}
