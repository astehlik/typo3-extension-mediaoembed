<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Exception;

class HttpClientRequestException extends \RuntimeException
{
    /**
     * @var string
     */
    private $errorDetails;

    public function __construct(string $message, int $httpCode, \Throwable $previous = null, string $errorDetails = '')
    {
        parent::__construct($message, $httpCode, $previous);

        $this->errorDetails = $errorDetails;
    }

    public function getErrorDetails(): string
    {
        return $this->errorDetails;
    }
}
