<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Exception;

use RuntimeException;
use Throwable;

class HttpClientRequestException extends RuntimeException
{
    public function __construct(string $message, int $httpCode, ?Throwable $previous = null)
    {
        parent::__construct($message, $httpCode, $previous);
    }
}
