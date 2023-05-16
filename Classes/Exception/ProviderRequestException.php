<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Exception;

use Sto\Mediaoembed\Domain\Model\Provider;

class ProviderRequestException extends OEmbedException
{
    public function __construct(
        private readonly Provider $provider,
        private readonly RequestException $exception
    ) {
        parent::__construct('Provider resolve failed: ' . $this->exception->getMessage(), 1684267448, $this->exception);
    }

    public function getException(): RequestException
    {
        return $this->exception;
    }

    public function getProvider(): Provider
    {
        return $this->provider;
    }
}
