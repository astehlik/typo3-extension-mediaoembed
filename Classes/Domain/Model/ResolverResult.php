<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Domain\Model;

use Sto\Mediaoembed\Response\GenericResponse;

class ResolverResult
{
    public function __construct(
        private readonly ?GenericResponse $response,
        private readonly ?Provider $provider,
    ) {
    }

    public function getProvider(): ?Provider
    {
        return $this->provider;
    }

    public function getResponse(): ?GenericResponse
    {
        return $this->response;
    }

    public function hasResponse(): bool
    {
        return $this->response !== null;
    }
}
