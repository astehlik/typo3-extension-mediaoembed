<?php

namespace Sto\Mediaoembed\Exception;

use RuntimeException;
use Throwable;

class HttpClientRequestException extends RuntimeException
{
    /**
     * @var string
     */
    private $errorDetails;

    public function __construct(string $message, int $httpCode, Throwable $previous = null, string $errorDetails = '')
    {
        parent::__construct($message, $httpCode, $previous);

        $this->errorDetails = $errorDetails;
    }

    public function getErrorDetails(): string
    {
        return $this->errorDetails;
    }
}
