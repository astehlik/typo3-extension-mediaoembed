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

use Sto\Mediaoembed\Domain\Model\Provider;

/**
 * This Exception will be thrown if no endpoint can be determined for a provider.
 */
class NoProviderEndpointException extends OEmbedException
{
    /**
     * Initializes the Exception with a default message and a default code (1303937972).
     *
     * @param Provider $provider
     */
    public function __construct($provider)
    {
        $message = 'No endpoints were found for provider %s.'
            . ' Please make sure you specify at least a native endpoint or enable a generic provider.';
        $message = sprintf($message, $provider->getName());
        parent::__construct($message, 1303937972);
    }
}
