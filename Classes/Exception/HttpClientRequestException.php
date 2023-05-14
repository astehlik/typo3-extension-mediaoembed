<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Exception;

class HttpClientRequestException extends \RuntimeException
{
    /**
     * @deprecated The param $errorDetails is ignored and will be removed in version 12.0.0.
     */
    public function __construct(string $message, int $httpCode, \Throwable $previous = null, string $errorDetails = '')
    {
        parent::__construct($message, $httpCode, $previous);
    }

    /**
     * @deprecated Not used any more, will always return an empty string. Will be removed in version 12.0.0.
     */
    public function getErrorDetails(): string
    {
        return '';
    }
}
