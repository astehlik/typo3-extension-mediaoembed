<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Provider;

class Endpoint
{
    private array $urlSchemes = [];

    public function __construct(
        private readonly string $name,
        private readonly string $url,
        private readonly bool $isRegex
    ) {}

    public function addUrlScheme(string $urlScheme): void
    {
        $this->urlSchemes[] = $urlScheme;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getUrlConfigKey(): string
    {
        return $this->isRegex() ? 'urlRegexes' : 'urlSchemes';
    }

    public function getUrlSchemes(): array
    {
        return $this->urlSchemes;
    }

    public function isRegex(): bool
    {
        return $this->isRegex;
    }
}
