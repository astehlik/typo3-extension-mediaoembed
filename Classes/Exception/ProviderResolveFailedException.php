<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Exception;

class ProviderResolveFailedException extends OEmbedException
{
    /**
     * @var ProviderRequestException[]
     */
    private array $requestExceptions;

    public function __construct(ProviderRequestException ...$requestExceptions)
    {
        parent::__construct('No provider could successfully resolve the URL');

        $this->requestExceptions = $requestExceptions;
    }

    /**
     * @return ProviderRequestException[]
     */
    public function getExceptions(): array
    {
        return $this->requestExceptions;
    }
}
