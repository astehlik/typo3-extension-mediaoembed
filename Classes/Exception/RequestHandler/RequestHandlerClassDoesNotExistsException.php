<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Exception\RequestHandler;

use Sto\Mediaoembed\Domain\Model\Provider;
use Sto\Mediaoembed\Exception\OEmbedException;
use Throwable;

final class RequestHandlerClassDoesNotExistsException extends OEmbedException
{
    public function __construct(Provider $provider)
    {
        $message = 'The configured renderer class %s of provider: %s does not exists.';
        $message = sprintf($message, $provider->getRequestHandlerClass(), $provider->getName());
        parent::__construct($message, 1620647813);
    }
}
