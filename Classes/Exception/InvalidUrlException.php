<?php
declare(strict_types=1);

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
 * This Exception will be thrown when an invalid URL is provided.
 */
class InvalidUrlException extends OEmbedException
{
    /**
     * Initializes the Exception with a default message and a default code (1303248111).
     *
     * @param string $url
     */
    public function __construct($url)
    {
        $message = 'The media URL %s is not a valid URL. Please make sure the URL is a valid http:// or https:// URL.';
        $message = sprintf($message, $url);
        parent::__construct($message, 1303248111);
    }
}
