<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Domain\Model;

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
 * An oEmbed provider.
 */
readonly class Provider
{
    public function __construct(
        private string $name,
        private string $endpoint,
        private array $urlSchemes,
        private bool $hasRegexUrlSchemes,
        private bool $displayDirectLink = true,
        private array $processors = [],
        private ?ProviderRequestHandlerConfig $requestHandlerConfig = null,
    ) {}

    public function getEndpoint(): string
    {
        return $this->endpoint;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return array|string[]
     */
    public function getProcessors(): array
    {
        return $this->processors;
    }

    public function getRequestHandlerClass(): string
    {
        return $this->requestHandlerConfig->requestHandlerClass ?? '';
    }

    public function getRequestHandlerSettings(): array
    {
        return $this->requestHandlerConfig->requestHandlerSettings ?? [];
    }

    public function getUrlSchemes(): array
    {
        return $this->urlSchemes;
    }

    public function hasRegexUrlSchemes(): bool
    {
        return $this->hasRegexUrlSchemes;
    }

    public function shouldDirectLinkBeDisplayed(): bool
    {
        return $this->displayDirectLink;
    }
}
